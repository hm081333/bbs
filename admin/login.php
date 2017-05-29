<!--<script src="../js/login.js"></script>-->
<?php
header("Content-type: text/html; charset=utf-8");
/**************************************/
/*		文件名：login.php		        */
/*		功能：管理员登录程序			*/
/**************************************/

require('../config.inc.php');
include('../header/admin.header.inc.php');
?>

<h3 class="center">后台登录</h3>

<fieldset>
	<legend>Login</legend>
	<div class="row">
		<form enctype="multipart/form-data" method="post" action="chklogin.php" class="col s12">

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">account_box</i>
					<input id="username" name="username" type="text">
					<label for="username">用户名</label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i>请输入用户名</p>
				</div>
			</div>

			<div class="col s12">
				<div class="input-field">
					<i class="material-icons prefix">vpn_key</i>
					<input id="password" name="password" type="password">
					<label for="password">密码</label>
				</div>
				<div>
					<p class="msg right"><i class="material-icons">warning</i>请输入密码</p>
				</div>
			</div>

			<div class="col s12 center">
				<button type="submit" name="submit" class="btn waves-effect waves-light">登录</button>
				<button type="reset" class="btn waves-effect waves-light">清空</button>
			</div>

		</form>
</fieldset>

<?php
//公用尾部页面
include('../header/footer.inc.php');
?>
