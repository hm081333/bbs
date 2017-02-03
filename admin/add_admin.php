<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**********************************/
  /*	   文件名：add_user.php		*/
  /*	   功能：添加用户记录   	*/
  /**********************************/
  require('../config.inc.php');

  //取得提交的数据，并做清理
  include ('./header.inc.php');
  //用户名
  $username	= ClearSpecialChars($_POST['username']);
  //密码
  $password	= $_POST['password'];
  $hash = password_hash($password, PASSWORD_BCRYPT);
  //账号权限设置
  $auth=$_POST['auth'];
  if ($auth == 'on') {
		$auth = '1';
	} 
	else {
		$auth = '0';
	}

  //检验数据的合法性
  if (!$username) {  
	echo '<script>alert(\'请输入用户名！\');window.history.back();</script>';
	exit();
  }
  elseif (!$password) { 
	echo '<script>alert(\'请输入密码！\');window.history.back();</script>';
	exit();
  }

  //判断用户是否已经存在
  $sql = "SELECT * FROM forum_admin WHERE username='$username'";
  $result = mysql_query($sql);
  $num_rows = mysql_num_rows($result);

  if ($num_rows > 0) {
	echo '<script>alert(\'该用户已经存在！点击确定返回重新添加\');window.history.back();</script>';
	exit();
  }

  //创建用户
  $sql = "INSERT INTO forum_admin (username,password,auth)VALUES('$username','$hash','$auth')";
  $result = mysql_query($sql);
  
  if($result)
  {
	?>
	
	<h3 class="center">添加管理员</h3>
	<p class="center">
    新管理员账号已经添加<br/>
    5秒后自动返回添加管理员页面
<script type="text/javascript">
setTimeout("window.history.back();",5000);
</script>
    
	<?php
	include('./footer.inc.php');		//尾文件
  }
  else {
	echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
	exit();
  }
?>
