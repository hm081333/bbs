<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：view_topic.php		*/
  /*		功能：文章详细页面			*/
  /**************************************/

  require('../config.inc.php');

  //根据ID取得贴子记录
  $id=$_GET['id'];
  $sql="SELECT * FROM forum_topic WHERE id='$id'";

  $result=mysqli_query($sql);
  $rows=mysqli_fetch_array($result);

  //记录不存在
  if (!$rows) 
  {
	echo '<script>alert(\'该贴记录不存在！\');window.history.back();</script>';
	exit();
  }

  //置顶标记
  $sticky=$rows['sticky'];

?>

<?php include('./header.inc.php'); ?>


<fieldset>
<legend>正文</legend>
<table>
<tr>
<td>

</td>
</tr>
<tr>
<td class="center">
<!--<a class="btn-floating waves-effect waves-light" style="float:left;margin-left: -10px;"><i id="x" class="material-icons">arrow_back</i></a>-->
<h4><?php echo '主题：'.$rows['topic'];?></h4>
</td>
</tr>
<tr>
<td class="center">
用户<a href="view_profile.php?id=<?php echo $rows['name'];?>">
<?php echo $rows['name'];?></a><br/><?php echo $rows['datetime'];?>
</td>
</tr>
<tr>
<td class="cyan lighten-3">
<?php
	//输出整理好的内容
	echo nl2br(htmlspecialchars($rows['detail']));
	echo '</br>';
?>

<?php
if(!empty($rows['pics'])){
?>
<img class="materialboxed" width="40%" src="../<?php echo $rows['pics']?>">
<?php
}else{
echo '';
}
?>
</td>
</tr>
<!--<p>创建于<?php echo $rows['datetime']; ?></p>-->

<tr>
<td class="lime lighten-4">
<dl>
<p><?php

  //获取回复内容
  $sql	="SELECT * FROM forum_reply WHERE topic_id='$id' order by reply_id";//order by xx根据回复先后顺序排序

  $result	= mysqli_query($sql);
  $num_rows = mysqli_num_rows($result);

  if ($num_rows)
  {
	//循环取出记录内容
	while($rows=mysqli_fetch_array($result))
	{
?>

 <dt>
    <a href="view_profile.php?id=<?php echo $rows['reply_name']; ?>">
    	<?php echo '用户'.$rows['reply_name']; ?>
    </a>
      <?php echo $rows['reply_datetime']; ?>
 </dt>
 <dd>
  <p><?php
    	//输出整理好的内容
    	echo nl2br(htmlspecialchars($rows['reply_detail'])); 
		
		echo '</br>';
		
     ?>
     
<?php
if(!empty($rows['reply_pics'])){
?>
<img class="materialboxed" width="30%" src="../<?php echo $rows['reply_pics']?>">
<?php
}else{
echo '';
}
?>

</p>
</dd>

 
 <?php
	}//结束循环
  }else{
	echo "&nbsp;&nbsp;&nbsp;&nbsp;暂无回复!";
  }
 
  //浏览量加1
  $sql = "UPDATE forum_topic set view=view+1 WHERE id='$id'";
  $result = mysqli_query($sql);

    ?></p>
</dl>
</td>
</tr>
</table>

<!--内容回复表单，开始-->


<div class="replyText"><?php 
//判断用户是否已经注册
if (!$_SESSION['username'])
{
	?>
    <p class="center">
	<a class="btn waves-effect waves-light" href="create_user.php">注册</a><br/>
    或<br/>
    <a class="btn waves-effect waves-light" href="login.php">登录</a><br/>
    进行评论
    </p>
    <?php
} else {

	
?>
<form enctype="multipart/form-data" method="post" action="add_reply.php">
<input name="id" type="hidden" value="<?php echo $id;?>">
<table class="blue lighten-5">
<tr>
<td>
<div class="input-field">
<textarea name="reply_detail" id="reply_detail" class="materialize-textarea validate"></textarea>
<label for="reply_detail">回帖内容</label>
<!--<textarea class="coolscrollbar" name="reply_detail" cols="80" rows="5"></textarea>-->
</td>
</tr>
<tr>
<td>
<div class="file-field input-field">
<div class="btn waves-effect waves-light">
<span>上传图片</span>
<input type="file" name="reply_pics" id="reply_pics" onchange="preview(this)">
</div>
<div class="file-path-wrapper">
<input class="file-path validate" type="text">
</div>
<div id="preview" class="center"></div>
</div>
</td>
</tr>
<tr>
<td class="center">
<button type="submit" name="Submit" class="btn waves-effect waves-light">回复该帖</button>
</td>
</tr>
</table>
</form>
<?php } ?></div>
<br>
<!--内容回复表单，结束-->

<?php 
  //如果是管理员用户，则输出“置顶”、“锁定”和“删除”按钮
  if ($_SESSION['user_auth'] == 1)
  { 
?>
<!--管理员操作表单，开始-->
<div class="center">
<p>管理员操作</p>


  <!--显示置顶操作按钮-->
  <?php if ($sticky == 0) { ?>
	<form name="stick" method="post" action="stick_topic.php">
	 <input type="hidden" name="id" value="<?php echo $id; ?>">
	 <button type="submit" name="Submit" class="btn waves-effect waves-light">置顶该贴</button><br/>
	 将该贴置于顶端
	</form>
  <?php } else { ?>
	<form name="unstick" method="post" action="unstick_topic.php">
	 <input type="hidden" name="id" value="<?php echo $id; ?>">
	 <button type="submit" name="Submit" class="btn waves-effect waves-light">取消置顶</button><br/>
	 取消该贴置顶
	</form>
  <?php } ?>
  
  <!--显示删除操作按钮-->
  <form name="delete" method="get" action="del_topic.php">
	 <input type="hidden" name="id" value="<?php echo $id; ?>">
	 <button type="submit" name="Submit" class="btn waves-effect waves-light">删除帖子</button><br/>
	 删除该帖与回复内容
  </form>
</div>
<!--管理员操作表单，结束-->
<?php 
	} 

?>
</fieldset>

<?php include('./footer.inc.php'); ?>
