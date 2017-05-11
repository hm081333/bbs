<?php
 ini_set("error_reporting","E_ALL & ~E_NOTICE"); /**************************************/
  /*		文件名：stick_topic.php		*/
  /*		功能：设置“置顶”操作		*/
  /**************************************/

  require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
	//取得文章ID
	$id=$_POST['id'];

	//设置“置顶”的SQL语句
	$sql = "UPDATE forum_topic SET sticky='1' WHERE id='$id'";

	$result=mysqli_query($sql);

	if($result)
	{
		//跳转页面
		echo '<script>alert(\'顶置成功\');window.history.back();</script>';
		exit();
	}
	else {
		echo '<script>alert(\'数据库操作错误！\');window.history.back();</script>';
	    exit();
	}

  } else {
	header("Location: ./");
  }
?>
