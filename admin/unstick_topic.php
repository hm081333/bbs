<?php
  /**************************************/
  /*		文件名：unstick_topic.php	*/
  /*		功能：取消“置顶”操作		*/
  /**************************************/

  require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
	//取得文章ID
	$id=$_POST['id'];

	//取消“置顶”的SQL语句
	$sql = "UPDATE forum_topic SET sticky='0' WHERE id='$id'";

	$result=mysqli_query($sql);

	if($result)
	{
		//跳转页面
		echo '<script>alert(\'取消顶置成功\');window.history.back();</script>';
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
