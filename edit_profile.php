<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8");
  /******************************************/
  /*		文件名：edit_profile.php		*/
  /*		功能：用户资料修改页面		    */
  /******************************************/

  require('./config.inc.php');

  //用户名
  $id = $_SESSION['username'];

  //如果用户没有登录
  if (!$_SESSION['username']) {
	echo '<script>alert(\'请登录后执行该请求！\');window.history.back();</script>';
	exit();
  }
?>

<?php include('./header.inc.php'); ?>

<h3 class="center">编辑个人资料</h3>

<?php
  //查询用户资料
  $sql="SELECT * FROM forum_user WHERE username = '$id'";
  $rows=fetch_once($sql);
?>

<fieldset>
<legend>个人资料</legend>
<br/>
<form enctype="multipart/form-data" method="post" action="update_profile.php">
<table width="100%">
  <tr>
    <td>登录用户:</td>
    <td><b><? echo $rows['username']; ?></b></td>
  </tr>
  <tr>
	<td width="15%">更新密码:</td>
    <td width="85%"><input placeholder="密码留空，将不被更新" name="password" type="password"></td>
  </tr>
  <tr>
	<td>电子邮件:</td>
	<td><input name="email" type="text" value="<?php echo $rows['email'];?>" class="validate"></td>
  </tr>
  <tr>
	<td>真实姓名:</td>
	<td><input name="realname" type="text" value="<?php echo $rows['realname'];?>" class="validate"></td>
  </tr>
<tr>
<td colspan="2" class="center">
<button type="submit" name="submit" class="btn waves-effect waves-light">更新</button>
</td>
</tr>
</table>
</form>
</fieldset>
<?php include('./footer.inc.php'); ?>
