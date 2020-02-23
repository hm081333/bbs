<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return [

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => [
        //'sign' => array('name' => 'sign', 'require' => true),
    ],

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => [
        'Site.Index',
    ],

    /**
     * 计划任务配置
     */
    'Task' => [
        //MQ队列设置，可根据使用需要配置
        'mq' => [
            'file' => [
                'path' => API_ROOT . '/Runtime',
                'prefix' => 'bbs_task',
            ],
            'redis' => [
                'host' => '127.0.0.1',
                'port' => 6379,
                'prefix' => 'bbs_task:',
                'auth' => '',
            ],
            'mc' => [
                'host' => '127.0.0.1',
                'port' => 11211,
            ],
        ],

        //Runner设置，如果使用远程调度方式，请加此配置
        'runner' => [
            'remote' => [
                'host' => 'http://library.phalapi.net/demo/',
                'timeoutMS' => 3000,
            ],
        ],
    ],

    /**
     * 扩展类库 - Redis扩展
     */
    /*'redis' => [
        //Redis链接配置项
        'servers' => [
            'host' => '127.0.0.1',        //Redis服务器地址
            'port' => '6379',             //Redis端口号
            'prefix' => 'bbs_',         //Redis-key前缀
            'auth' => '',          //Redis链接密码
        ],
        // Redis分库对应关系操作时直接使用名称无需使用数字来切换Redis库
        'DB' => [
            'developers' => 1,
            'user' => 2,
            'code' => 3,
        ],
        //使用阻塞式读取队列时的等待时间单位/秒
        'blocking' => 5,
    ],*/

    /**
     * 需要格式化的时间戳字段
     */
    'unix_time_format_field' => [
        'add_time',
        'reg_time',
        'edit_time',
        'end_time',
        'last_time',
        'birth_time',
        'refresh_time',
        'latest',
    ],
];
