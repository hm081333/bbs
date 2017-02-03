<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：add_topic.php		*/
  /*		功能：发表文章程序			*/
  /**************************************/

  require('./config.inc.php');

  if (!$_SESSION['username'])
  {
	//如果用户未登录，显示错误信息
	include('./header.inc.php');	//头文件
?>

<h2 class="center">创建新贴</h2>
<h3 class="center">您还没有登陆</h3>
<p class="center">
请<a href="create_user.php">注册</a>新用户，
或者进行<a href="login.php">登录</a>。
</p>

<?php 
	include('./footer.inc.php');		//尾文件

  } else {

	//获得用户信息
	$username = $_SESSION['username'];
	$sql = "SELECT * from forum_user WHERE username='$username'";
	$result = mysql_query($sql);
	$info = mysql_fetch_array($result);

	//取得传递来的值
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
    
	//电子邮件地址
    
	$email	= $info['email'];
 
	//是否置顶
   
	$sticky	= $_POST['sticky'];
    
	//是否锁定
    
//	$locked	= $_POST['locked'];
    
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
	//判断是否为锁定状态
//	if ($locked == "on" && $name == ADMIN_USER) { 
//		$locked = 1; 
//	}
//	else {
//		$locked = 0; 
//	}

	//判断是否置顶状态
	if ($sticky == "on" && $_SESSION['user_auth'] == 1) {
		$sticky = 1;
	} 
	else {
		$sticky = 0;
	}

	//将数据插入数据库
	$sql="INSERT INTO forum_topic(class_id,topic,detail,pics,name,email,datetime,sticky) VALUES('$class_id','$topic','$detail','$reback','$username','$email',NOW(),'$sticky')";
	$result=mysql_query($sql);
	if($result)
	{
		//成功后，跳转页面到论坛主页面
		header("Location: ./");
	}
	else 
	{
		echo '<script>alert(\'数据库错误！\');window.history.back();</script>';
	    exit();
	}
}
?>
