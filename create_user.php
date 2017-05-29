<script src="./js/reg.js"></script>
<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：create_user.php		*/
  /*		功能：生成用户注册页面		*/
  /**************************************/

  require('./config.inc.php');
  include('./header/header.inc.php');
?>

<h3 class="center">注册</h3>

<fieldset>
<legend>Register</legend>
<div class="row">
<form id="Register" enctype="multipart/form-data" method="post" action="add_user.php" class="col s12">

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">account_box</i>
<input id="username" name="username" type="text" class="validate">
<label for="username">用户名</label>
</div>
<div>
<p class="msg right"><i class="material-icons">warning</i>请输入6-26个字符长度</p>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">vpn_key</i>
<input id="password" name="password" type="password" class="validate">
<label for="password">密码</label>
</div>
<div>
<p class="msg right">密码长度不大于16</p>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">vpn_key</i>
<input id="password" name="password" type="password" class="validate" disabled="">
<label for="password">确认密码</label>
</div>
<div>
<p class="msg right">两次密码应当一致</p>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">email</i>
<input id="email" name="email" type="text" class="validate">
<label for="email">E-mail</label>
</div>
<div>
<p class="msg right"><i class="material-icons">warning</i>请输入6-26个字符长度</p>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">face</i>
<input id="realname" name="realname" type="text" class="validate">
<label for="realname">名字</label>
</div>
<div>
<p class="msg right">昵称应当唯一</p>
</div>
</div>

<div class="col s12 center">
<button type="submit" name="Submit" class="btn waves-effect waves-light">提交注册</button>
<button type="reset" class="btn waves-effect waves-light">重新输入</button>
</div>
</form>
</fieldset>

<?php 

	//公用尾部页面
	include('./header/footer.inc.php');
?>
