<?php
/**
 * 分库分表的自定义数据库路由配置
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2015-02-09
 */

return array(
	/**
	 * DB数据库服务器集群
	 * apt-cache search php7.0
	 */
	'servers' => array(
		'db_forum' => array(                         //服务器标记
			'host' => '123.249.20.195',             //数据库域名
			'name' => 'lyiho',               //数据库名字
			'user' => 'hm081333',                  //数据库用户名
			'password' => 'Lyihe110',                        //数据库密码
			'port' => '3306',                  //数据库端口
			'charset' => 'UTF8',                  //数据库字符集
		),
	),

	/**
	 * 自定义路由表
	 */
	'tables' => array(
		//通用路由
		'__default__' => array(
			'prefix' => 'ly_',
			'key' => 'id',
			'map' => array(
				array('db' => 'db_forum'),
			),
		),

		/*'forum' => array(                                                //表名
			'prefix' => 'tbl_',                                         //表名前缀
			'key' => 'id',                                              //表主键名
			'map' => array(                                             //表路由配置
				array('db' => 'db_forum'),                               //单表配置：array('db' => 服务器标记)
				array('start' => 0, 'end' => 2, 'db' => 'db_forum'),     //分表配置：array('start' => 开始下标, 'end' => 结束下标, 'db' => 服务器标记)
			),
		),*/
	),
);
