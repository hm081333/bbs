<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：index.php		*/
  /*		功能：论坛首页			*/
  /**************************************/

require('./config.inc.php');
  
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

<?php
include('./header.inc.php');
?>


<?php
//检索记录，按照置顶标记和时间排序
$sql = "SELECT * FROM forum_class order by id LIMIT $start, $each_page";
$result = mysql_query($sql);
?>

<h3 class="center">南洋交流平台</h3>
<table>
<thead>
<tr class="teal darken-3">
<th width="40%">课程</th>
<th>说明</th>
</tr>
</thead>

<?php
//循环输出输出记录列表
while($rows=mysql_fetch_array($result))
{
?>

<tbody>
<tr class="green accent-1">
<td>
<i class="material-icons">label</i><a class="brown-text" href="main_forum.php?<?php echo "id=".$rows['id'];?>&page=1"><b><?php echo $rows['name'];?></b></a>
</td>
<td>
<?php echo $rows['tips'];?>
<?php
}//退出while循环
?>
</td>
</tr>

<tr>
<td colspan="2">

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
  $sql = "SELECT COUNT(*) FROM forum_class";
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

//判断分页并输出
if ($prevpage || $nextpage) 
{
//上一页
if($prevpage)
{
?>
<a class="btn waves-effect waves-light" href="./?page=<?php echo $prevpage ?>"><i class="material-icons">arrow_back</i></a>
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
<a class="btn waves-effect waves-light" href="./?page=<?php echo $nextpage ?>"><i class="material-icons">arrow_forward</i></a>
<?php
}else{
?>
<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
<?php
}
}
?>

<a class="btn right waves-effect waves-light" onClick="location.href='create_topic.php'">发帖</a>

</td>
</tr>
</tbody>
</table>

<?php
//公用尾部页面
include('./footer.inc.php');
?>
