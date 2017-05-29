<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /**********************************/
  /*	   文件名：add_user.php		*/
  /*	   功能：添加注册用户记录	*/
  /**********************************/
  require('./config.inc.php');

  //取得提交的数据，并做清理
  include ('./header/header.inc.php');
  //用户名
  $username	= ClearSpecialChars($_POST['username']);
  //密码
  $password	= $_POST['password'];
  $hash = password_hash($password, PASSWORD_BCRYPT);
  //电子邮件地址
  $email		= ClearSpecialChars($_POST['email']);
  //真实姓名
  $realname	= ClearSpecialChars($_POST['realname']);

  //检验数据的合法性
  if (!$username) {
	echo '<script>alert(\'请输入用户名！\');window.history.back();</script>';
	exit();
  }
  if (!$password) {
	echo '<script>alert(\'请输入密码！\');window.history.back();</script>';
	exit();
  }
  if (!$email) {
	echo '<script>alert(\'请输入邮箱！\');window.history.back();</script>';
	exit();
  }
  elseif(!checkEmail($email)){
	echo '<script>alert(\'请输入正确邮箱！\');window.history.back();</script>';
	exit();
  }

  //判断用户是否已经存在
  $sql = "SELECT * FROM forum_user WHERE username='$username'";
  $num_rows = num_rows($sql);

  if ($num_rows > 0) {
	echo '<script>alert(\'该用户已经存在！点击确定返回重新注册\');window.history.back();</script>';
	exit();
  }

  //创建用户
  $sql = "INSERT INTO forum_user (username, password, email, realname, regdate)
		VALUES('$username', '$hash', '$email', '$realname', NOW())";
  $result = query($sql);

  if($result)
  {
	?>

	<h2 class="center">创建用户</h2>

	<p class="center">
    您的用户账号已经建立<br/>
    5秒后自动跳转首页<br/>
    或手动点击<a href="login.php">这里</a>登录</p>
<script type="text/javascript">
setTimeout("window.location.href='login.php'",5000);
</script>

	<?php
	include('./header/footer.inc.php');		//尾文件
  }
  else {
	echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
	exit();
  }
?>
