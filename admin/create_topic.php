<?php
header("Content-type: text/html; charset=utf-8");
ini_set("error_reporting", "E_ALL & ~E_NOTICE");
  /***********************************/
  /*      文件名：create_topic.php   */
  /*      功能：发表文章页面		 */
  /***********************************/

  require('../config.inc.php');
  include('../header/admin.header.inc.php');
  //判断用户是否登录，从而显示不同的界面
  if (isset($_SESSION["admin"])&&$_SESSION['admin']) { //登陆后显示页面
?>

<h3 class="center">添加新帖</h3>
<fieldset>
<legend>新帖子</legend>
<div class="row">
<form enctype="multipart/form-data" method="post" action="add_topic.php" class="col s12">

<div class="input-field col s12">
<input name="topic" type="text" id="topic" class="validate" length="100">
<label for="topic">标题</label>
</div>

<div class="input-field col s12">
<textarea name="detail" id="detail" class="materialize-textarea validate" length="1000"></textarea>
<label for="detail">正文内容</label>
</div>

<div class="file-field input-field col s8">
<div class="btn">
<span>上传图片</span>
<input type="file" name="pics" id="pics" onchange="preview(this)">
</div>
<div class="file-path-wrapper">
<input class="file-path validate" type="text">
</div>
</div>

<label>课程选择</label>
<select name="class_id" class="browser-default col s4">
<option disabled selected>请选择课程</option>
<?php
	$sql = "SELECT * FROM forum_class";
    $rows=fetch_all($sql);
	foreach ($rows as $row) {
		echo '<option value='.$row['id'].'>'.$row['name'].'</option>';
    }//退出while循环
?>
</select>

<div class="col s12 center">
<i class="material-icons">expand_more</i>
<b>上传图片预览</b>
<i class="material-icons">expand_more</i>
</div>

<div id="preview" class="center"></div>

<div class="center col s12" style="margin: 10;">
<div class="switch">
<label>
<b>不顶置</b>
<input type="checkbox" name="sticky" value="on">
<span class="lever"></span>
<b>顶置</b>
</label>
</div>
</div>

<br/>

<div class="col s12 center">
<button type="submit" name="Submit" class="btn waves-effect waves-light">立即发布</button>
<button type="reset" name="reset" class="btn waves-effect waves-light">重新输入</button>
</div>

</form>
</div>
</fieldset>

<?php

  } else {//未登陆返回登陆页面
    header("Location: ./");
  }
    //公用尾部页面
    include('../header/footer.inc.php');
?>
