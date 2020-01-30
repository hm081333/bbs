<?php

namespace Library;

/**
 * DependenceInjection 依赖注入类
 *
 *  Dependency Injection 依赖注入容器
 *
 * - 调用的方式有：set/get函数、魔法方法setX/getX、类变量$fdi->X、数组$fdi['X]
 * - 初始化的途径：直接赋值、类名、匿名函数
 *
 * <br>使用示例：<br>
 * ```
 *       $di = new DependenceInjection();
 *
 *       // 用的方式有：set/get函数  魔法方法setX/getX、类属性$di->X、数组$di['X']
 *       $di->key = 'value';
 *       $di['key'] = 'value';
 *       $di->set('key', 'value');
 *       $di->setKey('value');
 *
 *       echo $di->key;
 *       echo $di['key'];
 *       echo $di->get('key');
 *       echo $di->getKey();
 *
 *       // 初始化的途径：直接赋值、类名(会回调onInitialize函数)、匿名函数
 *       $di->simpleKey = array('value');
 *       $di->classKey = 'DependenceInjection';
 *       $di->closureKey = function () {
 *            return 'sth heavy ...';
 *       };
 * ```
 *
 * @property \PhalApi\Request                        $request         请求
 * @property \PhalApi\Response\JsonResponse          $response        结果响应
 * @property \Library\Serialize                      $serialize       序列化
 * @property \PhalApi\Cache\RedisCache               $cache           缓存
 * @property \Library\Crypt\RSA\Pub2PriCrypt         $crypt           加密
 * @property \PhalApi\Config                         $config          配置
 * @property \PhalApi\Logger                         $logger          日记
 * @property \Library\Database\NotORMDatabase        $notorm          数据库
 * @property \PhalApi\Loader                         $loader          自动加载
 * @property \PhalApi\Helper\Tracer                  $tracer          全球追踪器
 * @property \PhalApi\Redis\Lite                     $redis           Redis 拓展类
 * @property \PhalApi\Cookie                         $cookie          COOKIE操作
 * @property \Library\CUrl                           $curl            CURL请求类
 * @property \PhalApi\Tool                           $tool            工具集合类
 * @property \EasyWeChat\OfficialAccount\Application $wechat          微信工具
 * @package     \Library\DependenceInjection
 * @link        http://docs.phalconphp.com/en/latest/reference/di.html 实现统一的资源设置、获取与管理，支持延时加载
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-01
 */
class DependenceInjection extends \PhalApi\DependenceInjection
{
}

