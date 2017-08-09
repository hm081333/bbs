<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8;" charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<title><?php echo T('南洋师生交流平台DEMO'); ?></title>
	<link href="./Public/static/css/material-design-icons/3.0.1/material-icons.min.css" rel="stylesheet">
	<!--加载Material style图标-->
	<link href="./Public/static/css/materialize/0.99.0/materialize.min.css" type="text/css" rel="stylesheet"
		  media="screen,projection">
	<!--加载框架css-->
	<script src="./Public/static/js/jquery/3.2.1/jquery.min.js"></script><!--加载jQuery-->
	<script src="./Public/static/js/materialize/0.99.0/materialize.min.js"></script><!--加载框架js-->
	<link href="./Public/static/css/diy.css" rel="stylesheet"><!--加载自定义样式-->
</head>

<body><!-- style="background-image:url(./images/bgl.jpg);background-repeat:no-repeat;background-attachment:fixed;" -->
<!-- 头开始 -->
<header>
	<nav class="<!--hoverable--> cyan darken-4"><!--导航栏语句开始-->

		<div class="nav-wrapper container"><!--导航栏内容开始-->
			<?php if (!isset($back) && back) : ?>
				<a href="#" onclick="history.back();" class="button-collapse show-on-large"
				   style="float: left !important;">
					<i class="material-icons">arrow_back</i></a><!--网页LOGO-->
			<?php endif; ?>
			<a href="./" class="brand-logo">LYiHo</a><!--网页LOGO-->
			<ul class="right">
				<!--<li><a class="search_pic" href="#modal1"><i class="material-icons">search</i></a></li>-->
				<li><a class="dropdown-button" data-activates="language"><i class="material-icons">translate</i></a>
				</li>
				<li><a class="dropdown-button" data-activates="menu"><i class="material-icons">perm_identity</i></a>
				</li>
			</ul>
		</div>
	</nav><!--导航栏语句结束-->

	<ul id="menu" class="dropdown-content">
		<?php if (isset($_SESSION["user_name"])) : ?>
			<!-- 用户登录后 -->
			<li>
				<a href="?service=User.edit_Member"><?php echo $_SESSION['user_name']; ?></a>
			</li>
			<li class="divider"></li>
			<li><a onclick="logoff()"><?php echo T('退出登录'); ?></a></li>
		<?php else : ?>
			<!-- 用户未登录 -->
			<li><a href="?service=User.register"><?php echo T('注册'); ?></a></li>
			<li class="divider"></li>
			<li><a href="?service=User.login"><?php echo T('登录'); ?></a></li>
		<?php endif; ?>
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

	<!-- Modal Structure -->
	<!--<div id="modal1" class="modal bottom-sheet">
		<div class="modal-content row" style="text-align: center;">
			<h4><?php /*echo T('搜索'); */ ?></h4>
			<form id="search" method="post" onsubmit="return false;" class="col s12">
				<input type="hidden" name="service" value="public.search"/>
				<input type="hidden" name="action" value="post"/>
				<div class="col s12">
					<div class="input-field">
						<i class="material-icons prefix">label_outline</i>
						<input name="keyword" type="text" class="validate">
						<label for="keyword"><?php /*echo T('关键字'); */ ?></label>
					</div>
					<p>搜索条件：</p>
					<p>
						<input name="term" type="radio" id="topic" value="topic" class="with-gap" checked/>
						<label for="topic"><?php /*echo T('标题'); */ ?></label>
						<input name="term" type="radio" id="detail" value="detail" class="with-gap"/>
						<label for="detail"><?php /*echo T('正文内容'); */ ?></label>
						<input name="term" type="radio" id="user_name" value="user_name" class="with-gap"/>
						<label for="user_name"><?php /*echo T('用户名'); */ ?></label>
						<input name="term" type="radio" id="real_name" value="real_name" class="with-gap"/>
						<label for="real_name"><?php /*echo T('姓名'); */ ?></label>
					</p>
				</div>
				<div class="col s12 center">
					<button type="submit" name="submit"
							class="btn waves-effect waves-light"><?php /*echo T('搜索'); */ ?></button>
					<button type="reset" class="btn waves-effect waves-light"><?php /*echo T('重新输入'); */ ?></button>
				</div>
			</form>
		</div>
	</div>-->

</header>
<!-- 头结束 -->

<!-- 正文内容开始 -->
<main id="Content" class="container">
