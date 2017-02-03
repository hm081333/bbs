<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：main_forum.php		*/
  /*		功能：论坛主页面			*/
  /**************************************/

  require('../config.inc.php');
//取得当前页数
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

<?php include('./header.inc.php');?>

<h3 class="center">管理帖子</h3>

<table>
<thead>
<tr class="teal darken-3">
<th width="31%">帖子</th>
<th width="15%">课程</th>
<th width="12%">访问</th>
<th width="12%">回复</th>
<th width="25%">发表日期</th>
<th width="5%">操作</th>
</tr>
</thead>

<?php
//检索记录，按照置顶标记和时间排序
$sql = "SELECT * FROM forum_topic ORDER BY sticky DESC, datetime DESC LIMIT $start, $each_page";
$result = mysql_query($sql);

//循环输出输出记录列表
while($rows=mysql_fetch_array($result))
{ 
?>

<tbody>
<tr class="green accent-1">
<td>

<?php
	//如果是“置顶”的记录
	if ($rows['sticky'] == "1")
	{
	  ?><i class="material-icons">stars</i><?php 
	}
?>
<a href="view_topic.php?id=<?php echo $rows['id'];?>"><?php echo $rows['topic']; ?></a><br/><?php echo '发帖者: '.$rows['name']?>
</td>
<td>
<?php
$sql1="SELECT * FROM forum_class WHERE id=".$rows['class_id']."";
$result1=mysql_query($sql1);
$rows1=mysql_fetch_array($result1);
echo $rows1['name'];
?>
</td>
<td>
<?php 
echo $rows['view'];  //浏览量
?>
</td>
<td>
<?php 
echo $rows['reply'];  //回复量
?>
</td>
<td>
<?php 
echo $rows['datetime'];  //日期
?>
</td>
<td>
<!--显示置顶操作按钮-->
<?php if ($rows['sticky'] == 0) { ?>
<form enctype="multipart/form-data" method="post" action="stick_topic.php">
<input type="hidden" name="id" value="<?php echo $rows['id'];?>">
<button type="submit" name="submit" class="btn-floating waves-effect waves-light"><i class="material-icons">publish</i></button>
</form>
<?php } else { ?>
<form enctype="multipart/form-data" method="post" action="unstick_topic.php">
<input type="hidden" name="id" value="<?php echo $rows['id'];?>">
<button type="submit" name="submit" class="btn-floating waves-effect waves-light"><i class="material-icons">stars</i></button>
</form>
<?php } ?>  
<!--显示删除操作按钮-->
<form enctype="multipart/form-data" method="post" action="del_topic.php">
<input name="id" type="hidden" value="<?php echo $rows['id'];?>">
<button type="submit" name="submit" class="btn-floating waves-effect waves-light"><i class="material-icons">clear</i></button>
</form>
</td>
</tr>
<?php
  } //退出while循环
?>

<?php
  $prevpage = 0;
  //计算前一页
  if($page > 1)
  {
	$prevpage = $page - 1;
  }

  //当前记录
  $currentend = $start + EACH_PAGE;

  //取得所有的记录数
  $sql = "SELECT COUNT(*) FROM forum_topic";
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
<td colspan="6">
<?php
//判断分页并输出
if ($prevpage || $nextpage) 
{
//上一页
if($prevpage)
{
?>
<a class="btn waves-effect waves-light" href="./forum.php?page=<?php echo $prevpage;?>"><i class="material-icons">arrow_back</i></a>
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
<a class="btn waves-effect waves-light" href="./forum.php?page=<?php echo $nextpage;?>"><i class="material-icons">arrow_forward</i></a>
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
//公用尾部页面
include('./footer.inc.php') 
?>
