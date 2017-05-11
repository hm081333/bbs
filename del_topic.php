<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /**************************************/
  /*		文件名：del_topic.php		*/
  /*		功能：删除文章内容操作		*/
  /**************************************/

  require('./config.inc.php');

  //判断是否为管理员
  if ($_SESSION['user_auth'] == 1)
  {
	// get data that sent from form
	$id=$_GET['id'];

	//顺便删除帖子的图片
	$sql = "SELECT * FROM forum_topic WHERE id=$id";
	$rows=fetch_once($sql);
	if(!empty($rows['pics'])){
		$filename=$rows['pics'];
		unlink($filename);
	}

	//顺便删除回复中的图片
	$sql = "SELECT * FROM forum_reply WHERE topic_id=$id";
    $rows=fetch_all($sql);
	foreach($rows as $row){
		if(!empty($row['reply_pics'])){
			$filename=$row['reply_pics'];
			unlink($filename);
		}
	}

	//删除文章
	$sql = "DELETE FROM forum_topic WHERE id=$id";
	$result=query($sql);

	//删除回复内容
	$sql2 = "DELETE FROM forum_reply WHERE topic_id=$id";
	$result2=query($sql2);

	if($result && $result2)
	{
		//页面跳转
		header("Location: ./");
	}
	else {
	echo '<script>alert(\'数据库操作错误！\');window.history.back();</script>';
	exit();
	}
  } else {
	echo '<script>alert(\'你没有管理权限！\');window.history.back();</script>';
	exit();
  }
?>
