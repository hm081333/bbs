<?php
/**
 * 分库分表的自定义数据库路由配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author      : dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

return [
    /**
     * DB数据库服务器集群
     */
    'servers' => [
        'db_master' => [                       //服务器标记
            'type' => 'mysql',                 //数据库类型，暂时只支持：mysql, sqlserver
            'host' => '127.0.0.1',             //数据库域名
            'name' => 'bbs2',               //数据库名字
            'user' => 'root',                  //数据库用户名
            'password' => 'root',                        //数据库密码
            'port' => 3306,                    //数据库端口
            // 'charset' => 'UTF8',                  //数据库字符集
            'charset' => 'UTF8MB4',                  //数据库字符集
            'option' => [
                PDO::ATTR_PERSISTENT => false,
            ],
        ],
    ],

    /**
     * 自定义路由表
     */
    'tables' => [
        //通用路由
        '__default__' => [
            'prefix' => 'ly_',
            'key' => 'id',
            'map' => [
                ['db' => 'db_master'],
            ],
        ],

        /**
         * 'demo' => array(                                                //表名
         * 'prefix' => 'tbl_',                                         //表名前缀
         * 'key' => 'id',                                              //表主键名
         * 'map' => array(                                             //表路由配置
         * array('db' => 'db_master'),                               //单表配置：array('db' => 服务器标记)
         * array('start' => 0, 'end' => 2, 'db' => 'db_master'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
         * ),
         * ),
         */
        //10张表，可根据需要，自行调整表前缀、主键名和路由
        'task_mq' => [
            'prefix' => 'ly_',
            'key' => 'id',
            'map' => [
                ['db' => 'db_master'],
                ['start' => 0, 'end' => 9, 'db' => 'db_master'],
            ],
        ],
    ],
];
