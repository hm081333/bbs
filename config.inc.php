<?php
header("Content-type: text/html; charset=utf-8");
//开启SESSION
session_start();
	/*******************/
	/*  系统参数设置   */
	/*******************/

	//连接数据库的定义
	define('DB_USER',		 "root");		//用户名
	define('DB_PASSWORD',	 "root");		//密码
	define('DB_HOST',		 "localhost");	//数据库主机地址
	define('DB_NAME',		 "nyjl");	//数据库

	//分页设置，每页最多显示的记录数
	//define('$each_page',	 8);

//无效的字符，用于ClearSpecialChars()函数
	$invalidchars = array(
		"'",				//单引号
		";",				//分号
		"=",				//等号
		"\\",				//反斜线
		"/"					//斜线
	);



	/*******************/
	/*  公共函数设置   */
	/*******************/

	//功能：检查电子邮件地址格式是否正确
	//输入：电子邮件地址
	//输出：true或false
	function CheckEmail($email)
	{
		$check = "/^[0-9a-zA-Z_-]+@[0-9a-zA-Z_-]+(\.[0-9a-zA-Z_-]+){0,3}$/";

		if(preg_match ($check, $email)){
			return true;
		}
		else{
			return false;
		}
	}

//功能：清除字符串中的特殊字符
	//输入：字符串
	//输出：字符串
	function ClearSpecialChars($str)
	{
		global $invalidchars;

		$str = trim($str);
		$str = str_replace($invalidchars,"",$str);
		return $str;
	}


	/*******************/
	/*   初始化程序    */
	/*******************/

	//打开数据库连接
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if ($mysqli->connect_error) {
    	die('连接错误 (' . $mysqli->connect_errno . ') '. $mysqli->connect_error);
	}
	if (mysqli_connect_error()) {
		die('连接错误 (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
	}

	function query($sql)
	{
		global $mysqli;
		return $mysqli->query($sql);
	}
	function fetch_all($sql)
	{
		global $mysqli;
		return mysqli_fetch_all($mysqli->query($sql),MYSQLI_BOTH);
	}

	function fetch_once($sql)
	{
		global $mysqli;
		return mysqli_fetch_array($mysqli->query($sql),MYSQLI_BOTH);
	}

	function num_rows($sql)
	{
		global $mysqli;
		return mysqli_num_rows($mysqli->query($sql));
	}

?>
