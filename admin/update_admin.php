<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：update_profile.php	*/
  /*		功能：用户资料修改页面		*/
  /**************************************/

  require('../config.inc.php');
//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
include('./header.inc.php');
  //用户名
  $id =$_POST['id'];;
  //用户密码
  $password=$_POST['password'.$id.''];
  $hash = password_hash($password, PASSWORD_BCRYPT);
  //权限
  $auth=$_POST['auth'.$id.''];
  if ($auth == 'on') {
		$auth = '1';
	} 
	else {
		$auth = '0';
	}

  if (!$password) 
  {
	//如果密码为空，则不更新
	$sql="UPDATE forum_admin SET auth = '$auth' WHERE id = '$id'";
  } else {
	//如果输入了新的密码，则密码项更新
	$sql="UPDATE forum_admin SET password = '$hash',auth = '$auth' WHERE id = '$id'";
  }

  $result=mysql_query($sql);

  if($result){
?>

<h2 class="center">个人资料更新成功</h2>
<p class="center">
	您的个人资料已经被成功更新<br/> 
    5秒后自动返回上一页
</p>
<script type="text/javascript">

  setTimeout("window.history.back();",5000);

</script>

<?php
  }
  else {
	echo '<script>alert(\'记录不存在！\');window.history.back();</script>';
	exit();
  }
	}else{//未登陆返回登陆页面
	header("Location: ./");
	}
	//公用尾部页面
	include('./footer.inc.php'); 
?>
