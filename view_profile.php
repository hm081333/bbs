<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：view_profile.php	*/
  /*		功能：查看用户资料页面		*/
  /**************************************/
  require('./config.inc.php');

  //取得用户ID
  $id=$_GET['id'];

  //取得用户信息
  $sql="SELECT * FROM forum_user WHERE username='$id'";
  $result=mysql_query($sql);
  $rows=mysql_fetch_array($result);

  if (!$rows){
	echo '<script>alert(\'用户记录不存在！\');window.history.back();</script>';
	exit();
  }

  //正文内容
  $sql = "SELECT * FROM forum_topic WHERE name = '" . $id . "'";
  $count_q = mysql_query($sql);
  $num_count_q = mysql_num_rows($count_q);

  //回复内容
  $sql = "SELECT * FROM forum_reply WHERE reply_name = '" . $id . "'";
  $count_a = mysql_query($sql);
  $num_count_a = mysql_num_rows($count_a);

  //计算用户发表的帖子数量
  $num_count = $num_count_q + $num_count_a;
?>

<?php include('./header.inc.php'); ?>

<h3 class="center">查看 <b><?php echo $rows['username']; ?></b> 个人资料</h3>

<fieldset>
<legend>个人资料</legend>
<br/>
<table width="100%">
  <tr>
    <td width="30%">真实姓名:</td>
    <td width="70%"><?php echo $rows['realname'];?></td>
  </tr>
  <tr>
    <td>电子邮件:</td>
    <td><?php echo $rows['email'];?></td>
  </tr>
  <tr>
    <td>发贴数量:</td>
    <td><?php echo $num_count;?></td>
  </tr>
<tr>
<td colspan="2" class="center">
<button class="btn waves-effect waves-light" onclick="location.href='./'">返回首页</button>
</td>
</tr>
</table>
</fieldset>

<?php include('./footer.inc.php'); ?>
