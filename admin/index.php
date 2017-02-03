<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**********************************/
  /*		文件名：index.php		*/
  /*		功能：论坛首页			*/
  /**********************************/

  //判断用户是否登录，从而显示不同的界面
  session_start();
  if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
  { 
      include('user.php');				//直接包含
  }else{
	  include('login.php');
  }
  
?>
