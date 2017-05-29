<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>后台DEMO</title>
	<script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
	<!--加载jQuery-->
	<script type="text/javascript" src="../js/materialize.min.js"></script>
	<!--加载框架js-->
	<link href="../css/material-icons-3.0.1.css" rel="stylesheet">
	<!--加载Material style图标-->
	<link href="../css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection">
	<!--加载框架css-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<!--识别浏览设备-->
	<link href="../css/diy.css" rel="stylesheet">
	<!--加载自定义样式-->
	<script src="../js/diy.js"></script>
	<!--自定义JS脚本-->
</head>

<body>
<header>
	<nav class="hoverable cyan darken-4">
		<!--导航栏语句开始-->
		<div class="nav-wrapper container">
			<!--导航栏内容开始-->

			<script>
				(function ($) {
					$(function () {
						$(".button-collapse").sideNav();
						$('.collapsible').collapsible();
					}); // end of document ready
				})(jQuery); // end of jQuery name space
			</script>

			<!-- 头开始 -->
			<?php
			//判断用户是否登录，从而显示不同的导航界面
			if (isset($_SESSION["admin"]) && $_SESSION['admin']) {
				?>
				<!-- 用户登录后 -->
				<a href="" data-activates="slide-out" class="button-collapse show-on-large">
					<i class="material-icons">menu</i>
				</a>
				<ul id="slide-out" class="side-nav">
					<li class="no-padding">
						<ul class="collapsible collapsible-accordion">
							<li>
								<div class="userView">
									<div class="background">
										<img src="../images/office.jpg">
									</div>
									<a><img class="circle" src="../images/user.jpg"></a>
									<a><span class="white-text name">管理员：<?php echo $_SESSION['admin']; ?></span></a>
									<!-- <a href="#!email"><span class="white-text email">jdandturk@gmail.com</span></a> -->
								</div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<div class="divider"></div>
							</li>
						</ul>
						<!--<ul class="collapsible collapsible-accordion" style="height: 44px;width: 300px;">
							<li>
								<form>
									<div class="input-field">
										<i style="margin-top:-5px;" class="material-icons prefix">search</i>
										<input placeholder="搜索框" id="search" type="search">
										<i style="margin-top:-5px;" class="material-icons">close</i>
									</div>
								</form>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<div class="divider"></div>
							</li>
						</ul>-->
						<ul class="collapsible collapsible-accordion">
							<li>
								<a class="bold collapsible-header waves-effect waves-teal">会员管理<i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="./admin/create_user.php">添加用户</a></li>
										<li><a class="waves-effect waves-teal" href="./admin/user.php">管理用户</a></li>
										<li><a class="waves-effect waves-teal" href="./admin/create_admin.php">添加管理员</a></li>
										<li><a class="waves-effect waves-teal" href="./admin/admin.php">管理管理员</a></li>
										<?php /*$_SESSION['auth'] == 1 ? echo '
											<li><a class="waves-effect waves-teal" href="./admin/create_admin.php">添加管理员</a></li>
											<li><a class="waves-effect waves-teal" href="./admin/admin.php">管理管理员</a></li>
																				' : '';*/ ?>

									</ul>
								</div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<a class="bold collapsible-header waves-effect waves-teal">帖子管理<i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="./admin/create_topic.php">添加新贴</a></li>
										<li><a class="waves-effect waves-teal" href="./admin/forum.php">管理帖子</a></li>
									</ul>
								</div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<a class="bold collapsible-header waves-effect waves-teal">分类管理<i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="./admin/create_class.php">添加分类</a></li>
										<li><a class="waves-effect waves-teal" href="./admin/class.php">管理分类</a></li>
									</ul>
								</div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<div class="divider"></div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li><a class="subheader">前后台切换</a></li>
							<li><a class="waves-effect waves-teal" href="./admin/logoff.php">退出并返回前台</a></li>
						</ul>
					</li>
				</ul>
				<a href="./admin/" class="center brand-logo">后台</a>
				<ul class="right">
					<li><a class="search_pic" href="./admin/search.php"><i class="material-icons">search</i></a></li>
				</ul>
				<?php

			} else {
				?>
				<!-- 用户未登录 -->
				<a href="./admin/" class="center brand-logo">后台</a>
				</ul>
				<?php

			}//判断结束
			?>


		</div>
	</nav>
	<!--导航栏语句结束-->
</header>
<!-- 头结束 -->

<!-- 正文内容开始 -->
<div id="Content" class="container">
