<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：del_topic.php		*/
  /*		功能：删除文章内容操作		*/
  /**************************************/

  require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
	// get data that sent from form 
	$id=$_POST['id'];

	//删除文章
	$sql = "DELETE FROM forum_topic WHERE id=$id";
	$result=mysql_query($sql);

	//删除回复内容
	$sql2 = "DELETE FROM forum_reply WHERE topic_id=$id";
	$result2=mysql_query($sql2); 

	if($result && $result2)
	{
		//页面跳转
		echo '<script>alert(\'删除成功\');window.history.back();</script>';
		exit();
	}
	else {
	echo '<script>alert(\'数据库操作错误！\');window.history.back();</script>';
	exit();
	}
  } else {//未登陆返回登陆页面
	header("Location: ./");
  }
?>
