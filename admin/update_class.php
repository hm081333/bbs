<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：update_profile.php	*/
  /*		功能：用户资料修改页面	    	*/
  /**************************************/

  require('../config.inc.php');
//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
include('./header.inc.php');
  $id =$_POST['id'];;
  $name=$_POST['name'.$id.''];
  $tips=$_POST['tips'.$id.''];
  if (!$name) 
  {
	  echo '<script>alert(\'课程名为空！\');window.history.back();</script>';
	  exit();
  } else {
	//如果输入了新的密码，则密码项更新
	$sql="UPDATE forum_class SET name = '$name' WHERE tips = '$tips'";
  }

  $result=mysql_query($sql);

  if($result){
?>

<h2 class="center">课程更新成功</h2>
<p class="center">
	课程分类 已经被成功更新<br/> 
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
