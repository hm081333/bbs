<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /**************************************/
  /*		文件名：add_reply.php		*/
  /*		功能：回复文章保存页面		*/
  /***************************************/

  require('./config.inc.php');
  include('./header/header.inc.php');

//判断用户是否登录
  if(isset($_SESSION["username"])&&$_SESSION['username'])
  { //登陆后显示页面

  //回帖的ID
  $id=$_POST['id'];

  //验证帖子已经存在，未被锁定
  $sql = "SELECT * from forum_topic WHERE id='$id'";
  $topic_info = fetch_once($sql);

  if (!$topic_info)
  {
	echo '<script>alert(\'帖子记录不存在！\');window.history.back();</script>';
	exit();
  }


  //取得用户信息
  $username = $_SESSION['username'];
  $sql = "SELECT * from forum_user WHERE username='$username'";
  $user_info = fetch_once($sql);

  //取得提交过来的数据
  $reply_name=$_SESSION['username'];
  $reply_email=$user_info['email'];
  $reply_detail=$_POST['reply_detail'];

    //图片
	include_once './upfile.php';
	$fileinfo = $_FILES['reply_pics'];
	$reback = uppic($fileinfo);
	if($reback === false){
	echo '<script>alert(\'上传失败\');window.history.back();</script>';
	//echo '上传失败，类型错误，或超出大小';
	exit();
	}

  if (!$reply_detail)
  {
	echo '<script>alert(\'没有回贴内容！\');window.history.back();</script>';
	exit();
  }

  //取得reply_id的最大值
  $sql = "SELECT Count(reply_id) AS MaxReplyId FROM forum_reply WHERE topic_id='$id'";
  $rows=fetch_once($sql);
  //将reply_id最大值+1，如果没有该值，则设置为1。
  if ($rows)
  {
	$Max_id = $rows[0]+1;
  }
  else {
	$Max_id = 1;
  }

//图片
include_once './upfile.php';
$fileinfo = $_FILES['reply_pics'];
$reback = uppic($fileinfo);
if($reback === false){
echo '<script>alert(\'上传失败！\');window.history.back();</script>';
exit();
}

  //插入回复数据
  $sql="INSERT INTO forum_reply (topic_id,reply_id,reply_name,reply_email,reply_detail,reply_pics,reply_datetime)VALUES('$id','$Max_id','$reply_name','$reply_email','$reply_detail','$reback',NOW())";
  $result=query($sql);

  if($result)
  {
	//更新reply字段
	$sql="UPDATE forum_topic SET reply='$Max_id' WHERE id='$id'";
	$result=query($sql);

	//页面跳转
	header("Location: view_topic.php?id=$id");
  }
  else {
	echo '<script>alert(\'记录不存在！\');window.history.back();</script>';
	exit();
  }
  }else{//未登陆返回登陆页面
    echo '<script>alert(\'请登录后再回帖！\');window.history.back();</script>';
	exit();
  }
?>
