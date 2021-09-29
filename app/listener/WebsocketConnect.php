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

class WebsocketConnect
{
    /* @var $websocket Websocket */
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
        // $this->websocket->emit('server', $this->app->request->server());
        // $this->websocket->emit('header', $this->app->request->header());
        // $this->websocket->emit('cookie', $this->app->request->cookie());
        // dump($event);
        // $func = $event->type;
        // $this->$func($event->data);
    }

    public function __call($name, $arguments)
    {
        $this->websocket->emit('testcallback', ['msg' => '不存在的请求类型:' . $name]);
    }
}