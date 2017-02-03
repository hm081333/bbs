<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE");
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：main_forum.php		*/
  /*		功能：论坛主页面			*/
  /**************************************/

  require('./config.inc.php');
  
//根据课程类别ID取得贴子列表
$class_id=$_GET["id"];

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

<?php
//检索记录，按照置顶标记和时间排序
$sql = "SELECT * FROM forum_class where id='$class_id'";
$result = mysql_query($sql);
//循环输出输出记录列表
while($rows=mysql_fetch_array($result))
{
?>

<h3 class="center"><?php echo $rows['name'];?>区</h3>

<?php
}//退出while循环
?>

<table>
<thead>
<tr class="teal darken-3">
<th width="44%">帖子</th>
<th width="12%">访问</th>
<th width="12%">回复</th>
<th width="32%">发表日期</th>
</tr>
</thead>

<?php
//检索记录，按照置顶标记和时间排序
$sql = "SELECT * FROM forum_topic WHERE class_id='$class_id' ORDER BY sticky DESC, datetime DESC LIMIT $start, $each_page";
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
  $sql = "SELECT COUNT(*) FROM forum_topic where class_id='$class_id'";
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
<td colspan="4">
<?php
//判断分页并输出
if ($prevpage || $nextpage) 
{
//上一页
if($prevpage)
{
?>
<a class="btn waves-effect waves-light" href="./main_forum.php?<?php echo "id=".$class_id;?>&page=<?php echo $prevpage;?>"><i class="material-icons">arrow_back</i></a>
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
<a class="btn waves-effect waves-light" href="./main_forum.php?<?php echo "id=".$class_id;?>&page=<?php echo $nextpage;?>"><i class="material-icons">arrow_forward</i></a>
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
include('./footer.inc.php') 
?>
