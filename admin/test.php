<html>

<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<title>后台DEMO</title>
	<script type="text/javascript" src="../js/jquery-3.1.1.min.js"></script>
	<!--加载jQuery-->
	<script type="text/javascript" src="../js/materialize.js"></script>
	<!--加载框架js-->
	<link href="../css/material-icons-3.0.1.css" rel="stylesheet">
	<!--加载Material style图标-->
	<link href="../css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection">
	<!--加载框架css-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<!--识别浏览设备-->
	<script>
		(function($) {
			$(function() {
				/*$(".button-collapse").sideNav({
					menuWidth: 200, // Default is 300
					edge: 'left', // Choose the horizontal origin
					closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
					draggable: true // Choose whether you can drag to open on touch screens
				});*/
				$(".button-collapse").sideNav();
				$('.collapsible').collapsible();
			}); // end of document ready
		})(jQuery); // end of jQuery name space
	</script>
</head>

<body>
	<ul id="slide-out" class="side-nav">
		<li class="no-padding">
			<ul class="collapsible collapsible-accordion">
				<li>
					<div class="userView">
						<div class="background">
							<img src="../images/office.jpg">
						</div>
						<a href="#!user"><img class="circle" src="../images/user.jpg"></a>
						<a href="#!name"><span class="white-text name">John Doe</span></a>
						<a href="#!email"><span class="white-text email">jdandturk@gmail.com</span></a>
					</div>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<div class="divider"></div>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<form>
						<div class="input-field">
							<i style="margin-top:10px;" class="material-icons prefix">search</i>
							<input placeholder="搜索框" id="search" type="search" style="width: 60%;">
							<i style="margin-top:10px;" class="material-icons">close</i>
						</div>
					</form>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<div class="divider"></div>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<a class="collapsible-header waves-effect waves-teal">会员管理<i class="material-icons">arrow_drop_down</i></a>
					<div class="collapsible-body">
						<ul>
							<li><a class="waves-effect waves-teal" href="#!">添加用户</a></li>
							<li><a class="waves-effect waves-teal" href="#!">管理用户</a></li>
							<li><a class="waves-effect waves-teal" href="#!">添加管理员</a></li>
							<li><a class="waves-effect waves-teal" href="#!">管理管理员</a></li>
						</ul>
					</div>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<a class="collapsible-header waves-effect waves-teal">帖子管理<i class="material-icons">arrow_drop_down</i></a>
					<div class="collapsible-body">
						<ul>
							<li><a class="waves-effect waves-teal" href="#!">添加新贴</a></li>
							<li><a class="waves-effect waves-teal" href="#!">管理帖子</a></li>
						</ul>
					</div>
				</li>
			</ul>
			<ul class="collapsible collapsible-accordion">
				<li>
					<a class="collapsible-header waves-effect waves-teal">分类管理<i class="material-icons">arrow_drop_down</i></a>
					<div class="collapsible-body">
						<ul>
							<li><a class="waves-effect waves-teal" href="#!">添加分类</a></li>
							<li><a class="waves-effect waves-teal" href="#!">管理分类</a></li>
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
				<li><a class="waves-effect waves-teal" href="./logoff.php">退出并返回前台</a></li>
			</ul>
		</li>
	</ul>
	<a href="#" data-activates="slide-out" class="btn button-collapse">Side nav demo</a>
</body>
