<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types=1);

namespace library\trace;

use Closure;
use think\App;
use think\Config;
use think\event\LogWrite;
use think\Request;
use think\Response;
use think\response\Json;
use think\response\Redirect;

/**
 * 页面Trace中间件
 */
class TraceDebug
{

    /**
     * Trace日志
     * @var array
     */
    protected $log = [];

    /**
     * 配置参数
     * @var array
     */
    protected $config = [];

    /** @var App */
    protected $app;

    public function __construct(App $app, Config $config)
    {
        $this->app = $app;
        $this->config = $config->get('trace');
    }

    /**
     * 页面Trace调试
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return void
     */
    public function handle($request, Closure $next)
    {
        $debug = $this->app->isDebug();

        // 注册日志监听
        if ($debug) {
            $this->log = [];
            $this->app->event->listen(LogWrite::class, function ($event) {
                if (empty($this->config['channel']) || $this->config['channel'] == $event->channel) {
                    $this->log = array_merge_recursive($this->log, $event->log);
                }
            });
        }

        $response = $next($request);

        // Trace调试注入
        if ($debug) {
            $this->traceDebug($response);
        }

        return $response;
    }

    public function traceDebug(Response &$response)
    {
        $data = $response->getData();
        $content = $response->getContent();
        $config = $this->config;
        $type = $config['type'] ?? 'Html';

        unset($config['type']);

        $trace = App::factory($type, '\\library\\trace\\', $config);

        if ($response instanceof Redirect) {
            //TODO 记录
        } else if ($response instanceof Json) {
            $trace = App::factory('Json', '\\library\\trace\\', $config);
            $log = $this->app->log->getLog($config['channel'] ?? '');
            $log = array_merge_recursive($this->log, $log);
            $output = $trace->output($this->app, $response, $log);
            $data['trace'] = $output;
            $content = json_encode($data);
        } else {
            $log = $this->app->log->getLog($config['channel'] ?? '');
            $log = array_merge_recursive($this->log, $log);
            $output = $trace->output($this->app, $response, $log);
            if (is_string($output)) {
                // trace调试信息注入
                $pos = strripos($content, '</body>');
                if (false !== $pos) {
                    $content = substr($content, 0, $pos) . $output . substr($content, $pos);
                } else {
                    $content = $content . $output;
                }
            }
        }
        $response->data($data)->content($content);
    }
}
