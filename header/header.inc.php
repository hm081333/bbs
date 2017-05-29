<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>南洋师生交流平台DEMO</title>
	<link href="./css/material-icons-3.0.1.css" rel="stylesheet"><!--加载Material style图标-->
	<link href="./css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"><!--加载框架css-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/><!--识别浏览设备-->
	<script src="./js/jquery-3.1.1.min.js"></script><!--加载jQuery-->
	<script src="./js/materialize.min.js"></script><!--加载框架js-->
	<link href="./css/diy.css" rel="stylesheet"><!--加载自定义样式-->
	<script src="./js/diy.js"></script><!--自定义JS脚本-->
</head>

<body><!-- style="background-image:url(./images/bgl.jpg);background-repeat:no-repeat;background-attachment:fixed;" -->

<!-- 头开始 -->
<?php
//判断用户是否登录，从而显示不同的导航界面

if (isset($_SESSION["username"]) && $_SESSION['username']) {
	?>

	<!-- 用户登录后 -->
	<ul id="menu" class="dropdown-content">
		<li><a href="./edit_profile.php"><?php echo $_SESSION['username']; ?></a></li>
		<li class="divider"></li>
		<li><a href="./logoff_user.php">退出登录</a></li>
	</ul>
<?php } else { ?>
	<!-- 用户未登录 -->
	<ul id="menu" class="dropdown-content">
		<li><a href="./create_user.php">注册</a></li>
		<li class="divider"></li>
		<li><a href="./login.php">登录</a></li>
	</ul>
	<?php
}//判断结束
?>

<nav class="hoverable cyan darken-4"><!--导航栏语句开始-->

	<div class="nav-wrapper container"><!--导航栏内容开始-->
		<a href="./" class="brand-logo">LYiHo</a><!--网页LOGO-->
		<ul class="right">
			<li><a class="search_pic" href="./search.php"><i class="material-icons">search</i></a></li>
			<li><a class="dropdown-button" data-activates="menu"><i class="material-icons">perm_identity</i></a></li>
		</ul>
	</div>

</nav><!--导航栏语句结束-->

<!-- 头结束 -->

<!-- 正文内容开始 -->
<div id="Content" class="container">
