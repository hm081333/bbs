<?php
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：create_class.php	    */
  /*		  功能：添加课程	    	 	*/
  /**************************************/

  require('../config.inc.php');
  include('../header/admin.header.inc.php');

  //判断用户是否登录，从而显示不同的界面
  if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
  { //登陆后显示页面
?>

<h3 class="center">添加课程分类</h3>

<fieldset>
<legend>Add Class</legend>
<div class="row">
<form enctype="multipart/form-data" method="post" action="add_class.php" class="col s12">

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">label_outline</i>
<input id="name" name="name" type="text" class="validate">
<label for="name">课程名</label>
</div>
</div>

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">label_outline</i>
<input id="tips" name="tips" type="text" class="validate">
<label for="tips">课程说明</label>
</div>
</div>

<div class="col s12 center">
<button type="submit" name="submit" class="btn waves-effect waves-light">添加课程</button>
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
