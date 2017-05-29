<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：del_user.php		*/
  /*		功能：删除用户操作		*/
  /**************************************/

require('../config.inc.php');
//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
include('../header/admin.header.inc.php');

$id=$_POST['id'];
$sql = "DELETE FROM forum_admin WHERE id=$id";
$result=mysqli_query($sql);
if($result)
{
?>
<h2 class="center">删除管理员账户成功</h2>
<p class="center">
	您选择的管理员已经被成功删除<br/> 
    3秒后自动返回上一页
</p>
<script type="text/javascript">
setTimeout("window.history.back();",3000);
</script>
<?php
}
else {
echo '<script>alert(\'数据库操作错误！\');window.history.back();</script>';
exit();
}

}else{//未登陆返回登陆页面
header("Location: ./");
}
//公用尾部页面
include('../header/footer.inc.php'); 
?>
