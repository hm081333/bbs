<?php
declare (strict_types=1);

namespace app\listener;

use app\Request;
use think\App;
use think\Container;
use think\Event;
use think\exception\Handle;
use think\facade\Cache;
use think\Http;
use think\Response;
use think\response\Html;
use think\swoole\Manager;
use think\swoole\Websocket;
use Throwable;

class WebsocketEvent
{
    /* @var $websocket Websocket */
    private $websocket;
    private $app;
    /**
     * @var int
     */
    private $maxBinaryBufferSize = 4 * 1024;

    public function __construct(Container $container)
    {
        $this->app = $container;
        $this->websocket = $this->app->make(Websocket::class);
    }

    /**
     * 事件监听处理
     * @param $event
     */
    public function handle($event)
    {
        // dump($event->type);
        $func = $event->type;
        $this->$func($event->data);
    }

    /**
     * 设置令牌
     * @param $data
     */
    public function auth($data)
    {
        $data = $data[0] ?? false;
        if ($data) {
            $auth = base64_decode($data);
            $auth = compress_binary_decode($auth);
            $header = $this->app->request->header();
            $header['auth'] = $auth;
            $this->app->request->withHeader($header);
        }
    }

    //region 调用接口
    public function api($data)
    {
        $request_data = $data[0] ?? [];
        if (is_string($request_data)) $request_data = json_decode($request_data, true);
        $this->apiHandle($request_data);
    }

    protected function apiHandle(array $request_data)
    {
        $sign = md5(($request_data['s'] ?? '') . ($request_data['t'] ?? ''));
        if (isset($request_data['sign']) && $request_data['sign'] != $sign) return false;
        $manager = $this->app->make(Manager::class);
        $manager->runWithBarrier([
            $manager,
            'runInSandbox',
        ], function (Http $http, Event $event, \think\swoole\App $app) use ($request_data, $sign) {
            $app->setInConsole(false);

            /* @var $request \app\Request */
            $request = $this->prepareRequest($request_data);
            $request->websocket = $this->websocket;

            $_SERVER['VAR_DUMPER_FORMAT'] = 'html';
            try {
                $response = $this->handleRequest($http, $request);
            } catch (Throwable $e) {
                $response = $this->app
                    ->make(Handle::class)
                    ->render($request, $e);
            } finally {
                unset($_SERVER['VAR_DUMPER_FORMAT']);
            }
            // var_dump($request->getCurrentUser());

            $response_data = $response->getData() ?: $response->getContent();
            if (isset($request_data['sign']) && is_array($response_data)) {
                $response_data['sign'] = $sign;
                $this->sendToClient($sign, $response_data);
            } else if ($response instanceof Html) {
                $this->websocket->emit('html', $response_data);
            } else {
                $this->websocket->emit($sign, $response_data);
            }
        });
    }

    protected function prepareRequest(array $data)
    {
        $req = $this->app->request;
        $header = $req->header();
        $server = $req->server();
        $header['isapp'] = '1';
        $header['x-requested-with'] = 'XMLHttpRequest';
        $header['content-type'] = 'application/json; charset=UTF-8';
        $header['accept'] = 'application/json, text/plain, */*';

        foreach ($header as $key => $value) {
            $server['http_' . str_replace('-', '_', $key)] = $value;
        }
        unset($server['QUERY_STRING']);
        $server['REQUEST_METHOD'] = 'POST';
        $server['REQUEST_URI'] = '/';
        $server['PATH_INFO'] = '/';

        // 重新实例化请求对象 处理swoole请求数据
        /** @var \think\Request $request */
        $request = $this->app->make('request', [], true);

        return $request
            ->withHeader($header)
            ->withServer($server)
            ->withGet([])
            ->withPost($data)
            ->withCookie($req->cookie())
            ->withFiles($req->file() ?: [])
            ->withInput(json_encode($data))
            ->setBaseUrl('/')
            ->setUrl('/')
            ->setPathinfo('');
    }

    protected function handleRequest(Http $http, $request)
    {
        $level = ob_get_level();
        ob_start();

        $response = $http->run($request);

        $content = $response->getContent();

        if (ob_get_level() == 0) {
            ob_start();
        }

        $http->end($response);

        if (ob_get_length() > 0) {
            $response->content(ob_get_contents() . $content);
        }

        while (ob_get_level() > $level) {
            ob_end_clean();
        }

        return $response;
    }

    //endregion

    /**
     * 下发消息到客户端
     * @param string       $client_id
     * @param string|array $data
     * @return void
     */
    public function sendToClient($event, $data)
    {
        $data = $this->apiRequestParse($data);
        $split_data = str_split($data, $this->maxBinaryBufferSize);
        if (count($split_data) > 1) {
            // $micro_time = floor(microtime(true) * 1000);
            foreach ($split_data as $data_index => $data_split) {
                $this->websocket->emit('long_data', $this->apiRequestParse([
                    'is_end' => $data_index >= (count($split_data) - 1) ? true : false,
                    'index' => $data_index,
                    // 'time' => $micro_time,
                    'event' => $event,
                    'data' => $data_split,
                    // 'data' => $data_split,
                ]));
            }
        } else {
            dump($event, $data);
            $this->websocket->emit($event, $data);
        }
    }

    /**
     * 接收长消息
     * @param $data
     */
    public function long_data($data)
    {
        $data = $data[0] ?? [];
        $data = $this->apiResponseParse($data);
        /* @var $redis \Redis */
        $redis = Cache::handler();
        $redis_key = Cache::getCacheKey('long_data:' . $data['event']);
        $redis->zAdd($redis_key, $data['index'], $data['data']);
        if ($data['is_end']) {
            $long_data_arr = $redis->zRange($redis_key, 0, -1);
            $redis->del($redis_key);
            $long_data = implode('', $long_data_arr);
            $long_data = $this->apiResponseParse($long_data);
            $this->apiHandle($long_data);
        }

    }

    //region 接口数据处理
    protected function apiRequestParse($request)
    {
        $request = is_array($request) ? json_encode($request) : $request;
        return base64_encode(compress_binary_encode($request, ZLIB_ENCODING_RAW));
    }

    protected function apiResponseParse($response)
    {
        return json_decode(compress_binary_decode(base64_decode($response)), true);
    }
    //endregion

    //region 测试代码

    /**
     * 测试类型
     * @param $event
     */
    public function test($data)
    {
        $this->websocket->emit('testcallback', ['aaaaa' => 1, 'getdata' => '123123']);
        $this->websocket->to('roomtest')->emit('testcallback', '房间推送 ');
    }

    /**
     * 加入房间
     * @param $event
     */
    public function join($data)
    {
        $this->websocket->join($data[0]['room']);
    }

    /**
     * 离开房间
     * @param $event
     */
    public function leave($data)
    {
        $this->websocket->leave($data[0]['room']);
    }

    //endregion

    public function __call($name, $arguments)
    {
        $this->websocket->emit('testcallback', ['msg' => '不存在的请求类型:' . $name]);
    }
}
