<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>后台DEMO</title>
<link href="../css/material-icons-3.0.1.css" rel="stylesheet"><!--加载Material style图标-->
<link href="../css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"><!--加载框架css-->
<meta name="viewport" content="width=device-width, initial-scale=1.0"/><!--识别浏览设备-->
<script src="../js/jquery-3.1.1.min.js"></script><!--加载jQuery-->
<script src="../js/materialize.min.js"></script><!--加载框架js-->
<link href="../css/diy.css" rel="stylesheet"><!--加载自定义样式-->
<script src="./js/diy.js"></script><!--自定义JS脚本-->
</head>

<body>

<nav class="hoverable cyan darken-4"><!--导航栏语句开始-->
<div class="nav-wrapper container"><!--导航栏内容开始-->

<script>
$( document ).ready(function(){
$(".button-collapse").sideNav();
})
</script>

<!-- 头开始 -->
<?php 
  //判断用户是否登录，从而显示不同的导航界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ 
?>  
<!-- 用户登录后 -->
<a href="#" data-activates="slide-out" class="button-collapse show-on-large">
<i class="material-icons">menu</i>
</a>
<ul class="side-nav" id="slide-out">
<li>
<div class="userView">
<img class="background" src="../images/office.jpg">
<a><img class="circle" src="../images/user.jpg"></a>
<a><span class="white-text name">管理员：<?php echo $_SESSION['admin'];?></span></a>
<!--<a href="./logoff_user.php"><span class="white-text email">退出登陆</span></a>-->
</div>
</li>

<!--搜索框-->
<!--<li>
<a class="no-padding">
<form>
<div class="input-field">
<i style="margin-top:-8px;" class="material-icons prefix">search</i>
<input placeholder="搜索框" id="search" type="search">
<i style="margin-top:-8px;" class="material-icons">close</i>
</div>
</form>
</a>
</li>-->

<li><div class="divider"></div></li>

<li class="no-padding">
<ul class="collapsible collapsible-accordion">
<li class="bold"><a class="collapsible-header waves-effect waves-teal">用户管理</a>
<div class="collapsible-body">
<ul>
<li><a href="./create_user.php">添加用户</a></li>
<li><a href="./user.php">管理用户</a></li>

<?php
if($_SESSION['auth']==1){
?>
<li><a href="./create_admin.php">添加管理员</a></li>
<li><a href="./admin.php">管理管理员</a></li>
<?php
}
?>
</ul>
</div>
</li>

<li class="bold"><a class="collapsible-header waves-effect waves-teal">帖子管理</a>
<div class="collapsible-body">
<ul>
<li><a href="./create_topic.php">添加新贴</a></li>
<li><a href="./forum.php">管理贴子</a></li>
</ul>
</div>
</li>

<li class="bold"><a class="collapsible-header waves-effect waves-teal">分类管理</a>
<div class="collapsible-body">
<ul>
<li><a href="./create_class.php">添加分类</a></li>
<li><a href="./class.php">管理分类</a></li>
</ul>
</div>
</li>

</ul>
</li>

<li><div class="divider"></div></li>
<li><a class="subheader">前后台切换</a></li>
<li><a class="waves-effect waves-teal" href="./logoff.php">退出并返回前台</a></li>
</ul>
<a href="./" class="center brand-logo">后台</a>
<ul class="right">
<li><a class="search_pic" href="./search.php"><i class="material-icons">search</i></a></li>
</ul>
<?php } else { ?> 
<!-- 用户未登录 -->
<a href="./" class="center brand-logo">后台</a>
</ul>
<?php  
}//判断结束
?>



</div>
</nav><!--导航栏语句结束-->

<!-- 头结束 -->

<!-- 正文内容开始 -->
<div id="Content" class="container">