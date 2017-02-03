<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：update_profile.php	*/
  /*		功能：用户资料修改页面		*/
  /**************************************/

  require('./config.inc.php');
  include ('./header.inc.php');
  if (!$_SESSION['username']) {
	echo '<script>alert(\'请登录后执行该请求！\');window.history.back();</script>';
	exit();
  }

  //用户名
  $username=$_SESSION['username'];
  //电子邮件
  $email=$_POST['email'];
  //真实姓名
  $realname=$_POST['realname'];
  //用户密码
  $password=$_POST['password'];

  if (!$password) 
  {
	//如果密码为空，则密码项不予更新
	$sql="UPDATE forum_user SET email = '$email', realname = '$realname' WHERE username = '$username'";
  } else {
	//如果输入了新的密码，则密码项也予以更新
	$sql="UPDATE forum_user SET password = '$password', email = '$email', realname = '$realname' WHERE username = '$username'";
  }

  $result=mysql_query($sql);

  if($result){
?>

<h2 class="center">个人资料更新成功</h2>

<p class="center">
	您的个人资料已经被成功更新<br/> 
    5秒后自动跳转首页<br/>
	或手动点击<a href="./">返回</a>论坛主页。
</p>
<script type="text/javascript">

  setTimeout("window.location.href='index.php'",5000);

</script>

<?php
  }
  else {
	echo '<script>alert(\'记录不存在！\');window.history.back();</script>';
	exit();
  }
include ('./footer.inc.php');
?>
