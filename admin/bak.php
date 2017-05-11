<?php
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：bak.php		    */
  /*		  功能：后台模板	    	 	*/
  /**************************************/

  require('../config.inc.php');
  include('./header.inc.php');

  //判断用户是否登录，从而显示不同的界面
  if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
  { //登陆后显示页面
      
	  
	  
  }else{//未登陆返回登陆页面
  header("Location: ./");
  }

  //公用尾部页面
  include('./footer.inc.php'); 
?>
