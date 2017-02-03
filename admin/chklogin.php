<?php
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：chklogin.php	    	*/
  /*	    功能：验证管理员登录程序		*/
  /**************************************/

  require('../config.inc.php');
  //公用头部页面
  include('./header.inc.php');
	//用户名
	$username=ClearSpecialChars($_POST['username']);
	//密码，需要进行MD5加密
	$password=$_POST['password'];

	//从数据库中检索用户名，密码是否匹配
	$sql = "SELECT * FROM forum_admin WHERE username='$username'";
	$result = mysql_query($sql);
	$num_rows = mysql_num_rows($result);
	
	if($num_rows == 1)
	{
		//获得用户名
		$row = mysql_fetch_assoc($result);
		
		$hash = $row['password'];
		if(password_verify($password,$hash)){ 

		//将用户名存如SESSION中
		$_SESSION['admin'] = $row['username'];
		$_SESSION['auth'] = $row['auth'];

		//跳转到论坛主页面
		header("Location: index.php");
		}else{
			echo '<script>alert(\'用户名错误！\');window.history.back();</script>';
			exit();
		}
	}
	else {
		echo '<script>alert(\'用户名或者密码错误！\');window.location.href="logon_form.php";</script>';
	    exit();
	}

  //公用尾部页面
  include('./footer.inc.php'); 
?>
