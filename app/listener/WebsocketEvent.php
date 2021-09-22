<?php
declare (strict_types=1);

namespace app\listener;

use app\Request;
use think\App;
use think\Container;
use think\Event;
use think\exception\Handle;
use think\Http;
use think\Response;
use think\swoole\Websocket;
use Throwable;

class WebsocketEvent
{
    private $websocket;
    private $app;

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

    //region 调用接口
    public function api($data)
    {
        $requestData = $data[0] ?? [];
        $manager = $this->app->make(\think\swoole\Manager::class);
        $manager->runWithBarrier([$manager, 'runInSandbox'], function (Http $http, Event $event, \think\swoole\App $app) use ($requestData) {
            $app->setInConsole(false);

            $request = $this->prepareRequest($requestData);

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

            // file_put_contents(public_path() . '/error.html', $response->getData());
            // $this->websocket->emit('api', $response->getData());
            $this->websocket->emit(md5($requestData['s'] . $requestData['t']), $response->getData());
        });
    }

    protected function prepareRequest($data)
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

    public function connect()
    {
        $this->websocket->emit('connect', ['aaaaa' => 1, 'getdata' => '123123']);
    }

    /**
     * 测试类型
     * @param $event
     */
    public function test($data)
    {
        $this->websocket->emit('testcallback', ['aaaaa' => 1, 'getdata' => '123123']);
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

    public function __call($name, $arguments)
    {
        $this->websocket->emit('testcallback', ['msg' => '不存在的请求类型:' . $name]);
    }
}