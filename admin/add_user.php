<?php
ini_set("error_reporting", "E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /**********************************/
  /*	   文件名：add_user.php		*/
  /*	   功能：添加用户记录   	*/
  /**********************************/
  require('../config.inc.php');

  //取得提交的数据，并做清理
  include('../header/admin.header.inc.php');
  //用户名
  $username    = ClearSpecialChars($_POST['username']);
  //密码
  $password    = $_POST['password'];
  $hash = password_hash($password, PASSWORD_BCRYPT);
  //权限
  $auth=$_POST['auth'];
  if ($auth == 'on') {
      $auth = '1';
  } else {
        $auth = '0';
    }
  //电子邮件地址
  $email        = ClearSpecialChars($_POST['email']);
  //真实姓名
  $realname    = ClearSpecialChars($_POST['realname']);

  //检验数据的合法性
  if (!$username) {
      echo '<script>alert(\'请输入用户名！\');window.history.back();</script>';
      exit();
  } elseif (!$password) {
      echo '<script>alert(\'请输入密码！\');window.history.back();</script>';
      exit();
  }
/*  if (!$email) {
    echo '<script>alert(\'请输入电子邮件地址！\');window.history.back();</script>';
    exit();
  }*/
/*  elseif(!checkEmail($email)){
    echo '<script>alert(\'电子邮件地址格式错误！\');window.history.back();</script>';
    exit();
  }*/

  //判断用户是否已经存在
  $sql = "SELECT * FROM forum_user WHERE username='$username'";
  $num_rows = num_rows($sql);

  if ($num_rows > 0) {
      echo '<script>alert(\'该用户已经存在！点击确定返回重新添加\');window.history.back();</script>';
      exit();
  }

  //创建用户
  $sql = "INSERT INTO forum_user (username,password,email,realname,auth,regdate)
		VALUES('$username','$hash','$email','$realname','$auth',NOW())";
  $result = query($sql);

  if ($result) {
      ?>

	<h3 class="center">添加用户</h3>
	<p class="center">
    新用户账号已经添加<br/>
    5秒后自动返回添加用户页面
<script type="text/javascript">
setTimeout("window.history.back();",5000);
</script>

	<?php
    include('../header/footer.inc.php');        //尾文件
  } else {
      echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
      exit();
  }
?>
