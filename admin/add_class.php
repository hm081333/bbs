<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /**************************************/
  /*		文件名：add_class.php		    */
  /*		  功能：添加课程	    	 	*/
  /**************************************/

  require('../config.inc.php');
  include('../header/admin.header.inc.php');

$name=$_POST['name'];
$tips=$_POST['tips'];
//检验数据的合法性
if (!$name) {
echo '<script>alert(\'请输入课程名！\');window.history.back();</script>';
exit();
}
//创建
$sql = "INSERT INTO forum_class (name,tips)VALUES('$name','$tips')";
$result = query($sql);

if($result)
{
?>

<h3 class="center">添加课程</h3>
<p class="center">
新课程已经添加<br/>
3秒后自动返回添加课程页面
<script type="text/javascript">
setTimeout("window.history.back();",3000);
</script>

<?php
include('../header/footer.inc.php');		//尾文件
}
else {
echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
exit();
}
?>
