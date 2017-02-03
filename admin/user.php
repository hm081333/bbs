<?php
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：user.php		    */
  /*		  功能：管理用户	    	 	*/
  /**************************************/

	require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if(isset($_SESSION["admin"])&&$_SESSION['admin']) 
{ //登陆后显示页面
include('./header.inc.php');
//取得当前页数
  
//  $page=$_GET['page'];
  $page=$_GET["page"];
 
  
  //每页最多显示的记录数
  $each_page = 8;

  //计算页面开始位置
  if(!$page || $page == 1)
  {
	$start = 0;
  }else{
	$offset = $page - 1;
	$start = ($offset * $each_page);
  }
?>

<h3 class="center">管理用户</h3>
<fieldset>
<legend>Manage User</legend>
<table>
<thead>
<th>用户名</td>
<th>权限</th>
<th>E-mail</td>
<th>名字</td>
<th>更改密码</th>
<th width="5%">操作</th>
</thead>
<tbody>
<?php
$sql="SELECT * FROM forum_user order by id LIMIT $start, $each_page";
$result = mysql_query($sql);
//循环输出输出记录列表
while($rows=mysql_fetch_array($result))
{ 
?>
<form enctype="multipart/form-data" method="post" action="update_user.php">
<input name="id" type="hidden" value="<?php echo $rows['id'];?>">
<tr>
<td>
<?php echo $rows['username'];?>
</td>
<td>
<?php
if ($rows['auth'] == '1') {
?>
<p>
<input name="auth<?php echo $rows['id'];?>" type="checkbox" class="filled-in" id="auth<?php echo $rows['id'];?>" checked/>
<label for="auth<?php echo $rows['id'];?>"></label>
</p>
<?PHP
} 
else {
?>
<p>
<input name="auth<?php echo $rows['id'];?>" type="checkbox" class="filled-in" id="auth<?php echo $rows['id'];?>"/>
<label for="auth<?php echo $rows['id'];?>"></label>
</p>
<?php
}
?>
</td>
<td>
<input name="email<?php echo $rows['id'];?>" type="text" value="<?php echo $rows['email'];?>">
</td>
<td>
<input name="realname<?php echo $rows['id'];?>" type="text" value="<?php echo $rows['realname'];?>">
</td>
<td>
<input placeholder="密码留空，将不被更新" name="password<?php echo $rows['id'];?>" type="password">	
</td>
<td>
<button type="submit" name="submit" class="btn-floating waves-effect waves-light">修改</button>
</form>
<form enctype="multipart/form-data" method="post" action="del_user.php">
<input name="id" type="hidden" value="<?php echo $rows['id'];?>">
<button type="submit" name="submit" class="btn-floating waves-effect waves-light">删除</button>
</form>
</td>
</tr>
<?php
} //退出while循环
  $prevpage = 0;
  //计算前一页
  if($page > 1)
  {
	$prevpage = $page - 1;
  }

  //当前记录
  $currentend = $start + EACH_PAGE;

  //取得所有的记录数
  $sql = "SELECT COUNT(*) FROM forum_user";
  $result = mysql_query($sql);
  $row = mysql_fetch_row($result);
  $total = $row[0];
  $nextpage = 0;
  //计算后一页
  if($total>$currentend)
  {
	if(!$page){
		$nextpage = 2;
	}else{
		$nextpage = $page + 1;
	}
  }
?>
<tr>
<td colspan="5">
<?php
//判断分页并输出
if ($prevpage || $nextpage) 
{
//上一页
if($prevpage)
{
?>
<a class="btn waves-effect waves-light" href="./user.php?page=<?php echo $prevpage ?>"><i class="material-icons">arrow_back</i></a>
<?php
}else{
?>
<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
<?php
}
//后一页
if($nextpage)
{
?>
<a class="btn waves-effect waves-light" href="./user.php?page=<?php echo $nextpage ?>"><i class="material-icons">arrow_forward</i></a>
<?php
}else{
?>
<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
<?php
}
}
?>
</td>
</tr>
</tbody>
</table>

<?php
	}else{//未登陆返回登陆页面
	header("Location: ./");
	}

	//公用尾部页面
	include('./footer.inc.php'); 
?>
