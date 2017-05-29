<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：create_user.php		*/
  /*		功能：生加用户页面				*/
  /**************************************/

  require('../config.inc.php');
  include('../header/admin.header.inc.php');
  //判断用户是否登录，从而显示不同的界面
  if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
  { //登陆后显示页面
?>

<h3 class="center">添加用户</h3>

<fieldset>
<legend>Add User</legend>
<div class="row">
<form id="Register" enctype="multipart/form-data" method="post" action="add_user.php" class="col s12">

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">account_box</i>
<input id="username" name="username" type="text" class="validate">
<label for="username">用户名</label>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">vpn_key</i>
<input id="password" name="password" type="password" class="validate">
<label for="password">密码</label>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">email</i>
<input id="email" name="email" type="text">
<label for="email">E-mail</label>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">face</i>
<input id="realname" name="realname" type="text">
<label for="realname">名字</label>
</div>
</div>

<div class="center">
<div class="switch">
<label>
<b>普通用户</b>
<input type="checkbox" name="auth">
<span class="lever"></span>
<b>权限用户</b>
</label>
</div>
</div>

<br/>

<div class="col s12 center">
<button type="submit" name="Submit" class="btn waves-effect waves-light">添加用户</button>
<button type="reset" class="btn waves-effect waves-light">重新输入</button>
</div>
</form>
</fieldset>

<?php
	}else{//未登陆返回登陆页面
	header("Location: ./");
	}
	//公用尾部页面
	include('../header/footer.inc.php');
?>
