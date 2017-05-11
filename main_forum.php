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
// $result = mysqli_query($sql);
// var_dump(fetch_once($sql));
//循环输出输出记录列表
$rows=fetch_once($sql);

?>

<h3 class="center"><?php echo $rows['name'];?>区</h3>

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
$result = fetch_all($sql);

//循环输出输出记录列表
foreach($result as $row)
{
?>

<tbody>
<tr class="green accent-1">
<td>

<?php
	//如果是“置顶”的记录
	if ($row['sticky'] == "1")
	{
	  ?><i class="material-icons">stars</i><?php
	}
?>
<a href="view_topic.php?id=<?php echo $row['id'];?>"><?php echo $row['topic']; ?></a><br/><?php echo '发帖者: '.$row['name']?>
</td>
  <td>
	  <?php
		echo $row['view'];  //浏览量
	  ?>
  </td>
  <td>
	  <?php
		echo $row['reply'];  //回复量
	  ?>
  </td>
  <td>
	  <?php
		echo $row['datetime'];  //日期
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
  $currentend = $start + $each_page;

  //取得所有的记录数
  $sql = "SELECT COUNT(*) AS c FROM forum_topic where class_id='$class_id'";
  $total = fetch_once($sql)['c'];
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
