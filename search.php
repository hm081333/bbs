<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
/**************************************/
/*		文件名：search.php		  */
/*		功能：搜索页面	      	  */
/**************************************/

require('./config.inc.php');
include('./header.inc.php');
?>

<h3 class="center">搜索</h3>
<fieldset>
<legend>Search</legend>
<div class="row">
<form enctype="multipart/form-data" method="post" action="search_result.php" class="col s12">

<div class="col s12">
<div class="input-field">
<i class="material-icons prefix">label_outline</i>
<input id="keyword" name="keyword" type="text" class="validate">
<label for="keyword">关键字</label>
</div>
<p>搜索条件：</p>
<p>
<input name="term" type="radio" id="topic" value="topic" class="with-gap" checked/>
<label for="topic">标题</label>
<input name="term" type="radio" id="detail" value="detail" class="with-gap"/>
<label for="detail">正文内容</label>
</p>
</div>

<div class="col s12 center">
<button type="submit" name="submit" class="btn waves-effect waves-light">搜索</button>
<button type="reset" class="btn waves-effect waves-light">重新输入</button>
</div>

</form>
</div>
</fieldset>

<?php
//公用尾部页面
include('./footer.inc.php');
?>