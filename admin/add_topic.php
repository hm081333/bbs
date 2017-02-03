<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：add_topic.php		*/
  /*		功能：发表文章程序			*/
  /**************************************/

  require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
include('./header.inc.php');

	//标题
	$topic= $_POST['topic'];	
      
	//正文
	$detail= $_POST['detail'];	
	 
	//图片
	include_once './upfile.php';
	$fileinfo = $_FILES['pics'];
	$reback = uppic($fileinfo);
	if($reback === false){
	echo '<script>alert(\'上传失败！\');window.history.back();</script>';
	exit();
	}
	
	//帖子类型
	$class_id = $_POST['class_id'];
    
	//发帖人
    
	$name	= '管理员';
    
	//是否置顶
   
	$sticky	= $_POST['sticky'];
    
	//数据合法性检查
	if (!$topic)
	{
		echo '<script>alert(\'请输入标题！\');window.history.back();</script>';
	    exit();
	}
	if (!$detail)
	{
		echo '<script>alert(\'请输入正文！\');window.history.back();</script>';
	    exit();
	}
    if (is_null($class_id))
	{
		echo '<script>alert(\'请选择问题课程！\');window.history.back();</script>';
	    exit();
	}

	//判断是否置顶状态
	if ($sticky == 'on') {
		$sticky = 1;
	} 
	else {
		$sticky = 0;
	}

//将数据插入数据库
$sql="INSERT INTO forum_topic(class_id,topic,detail,pics,name,datetime,sticky)VALUES('$class_id','$topic','$detail','$reback','$name',NOW(),'$sticky')";
$result=mysql_query($sql);
if($result)
{
?>
<h2 class="center">添加新帖成功</h2>
<p class="center">
	您添加的新帖已经成功添加<br/> 
    3秒后自动返回上一页
</p>
<script type="text/javascript">
setTimeout("window.history.back();",3000);
</script>
<?php
}
else 
{
echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
exit();
}
}else{//未登陆返回登陆页面
header("Location: ./");
}
//公用尾部页面
include('./footer.inc.php'); 
?>
