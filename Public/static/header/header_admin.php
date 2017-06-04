<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8;" charset="UTF-8">
	<!--识别浏览设备-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title><?php echo T('后台DEMO') ?></title>
	<script type="text/javascript" src="./Public/static/js/jquery.min.js"></script>
	<!--加载jQuery-->
	<script type="text/javascript" src="./Public/static/js/materialize.min.js"></script>
	<!--加载框架js-->
	<link href="./Public/static/css/material-icons-3.0.1.css" rel="stylesheet">
	<!--加载Material style图标-->
	<link href="./Public/static/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection">
	<!--加载框架css-->
	<link href="./Public/static/css/diy.css" rel="stylesheet">
	<!--加载自定义样式-->
	<script src="./Public/static/js/diy.js"></script>
	<!--自定义JS脚本-->
</head>

<body>
<header>
	<nav class="<!--hoverable--> cyan darken-4">
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
			<?php if (isset($_SESSION["admin_name"])) : //判断用户是否登录，从而显示不同的导航界面 ?>
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
										<img src="./Public/static/images/office.jpg">
									</div>
									<a><img class="circle" src="./Public/static/images/user.jpg"></a>
									<a><span class="white-text name"><?php echo T('管理员：') ?><?php echo $_SESSION['admin_name']; ?></span></a>
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
								<a class="bold collapsible-header waves-effect waves-teal"><?php echo T('会员管理') ?><i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="?service=User.register"><?php echo T('添加用户') ?></a>
										</li>
										<li><a class="waves-effect waves-teal" href="?service=Default.Index"><?php echo T('管理用户') ?></a></li>
										<li><a class="waves-effect waves-teal" href="?service=User.create_admin"><?php echo T('添加管理员') ?></a>
										</li>
										<li><a class="waves-effect waves-teal" href="?service=User.admin_list"><?php echo T('管理管理员') ?></a></li>
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
								<a class="bold collapsible-header waves-effect waves-teal"><?php echo T('帖子管理') ?><i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="?service=Topic.create_Topic"><?php echo T('添加新帖子') ?></a>
										</li>
										<li><a class="waves-effect waves-teal" href="?service=Topic.topic_List"><?php echo T('管理帖子') ?></a></li>
									</ul>
								</div>
							</li>
						</ul>
						<ul class="collapsible collapsible-accordion">
							<li>
								<a class="bold collapsible-header waves-effect waves-teal"><?php echo T('分类管理') ?><i
											class="material-icons">arrow_drop_down</i></a>
								<div class="collapsible-body">
									<ul>
										<li><a class="waves-effect waves-teal" href="?service=Class.create_Class"><?php echo T('添加分类') ?></a>
										</li>
										<li><a class="waves-effect waves-teal" href="?service=Class.class_List"><?php echo T('管理分类') ?></a></li>
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
							<li><a class="subheader"><?php echo T('退出登陆') ?></a></li>
							<li><a class="waves-effect waves-teal" onclick="logoff()"><?php echo T('退出登陆') ?></a></li>
						</ul>
					</li>
				</ul>
			<?php endif; ?>
				<a href="./admin.php" class="center brand-logo"><?php echo T('后台') ?></a>
			<ul class="right">
				<li><a class="dropdown-button" data-activates="language"><i class="material-icons">translate</i></a></li>
			</ul>
			<ul id="language" class="dropdown-content">
				<li>
					<a onclick="javascript:set_language('zh_cn')"><?php echo T('简体中文'); ?></a>
				</li>
				<li class="divider"></li>
				<li>
					<a onclick="javascript:set_language('zh_tw')"><?php echo T('繁体中文'); ?></a>
				</li>
				<li class="divider"></li>
				<li>
					<a onclick="javascript:set_language('en')"><?php echo T('英语'); ?></a>
				</li>
				<li class="divider"></li>
				<li>
					<!--de 德标 at 奥地利 ch 瑞士 ru 俄罗斯(欧境)-->
					<a onclick="javascript:set_language('de')"><?php echo T('德语'); ?></a>
				</li>
				<li class="divider"></li>
				<li>
					<!--fr 法标 lu 卢森堡-->
					<a onclick="javascript:set_language('fr')"><?php echo T('法语'); ?></a>
				</li>
			</ul>


		</div>
	</nav>
	<!--导航栏语句结束-->
</header>
<!-- 头结束 -->

<!-- 正文内容开始 -->
<div id="Content" class="container">
