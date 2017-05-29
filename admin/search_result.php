<?php
ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
header("Content-type: text/html; charset=utf-8"); 
  /**************************************/
  /*		文件名：search_result.php	    */
  /*		功能：搜索结果页面		     	*/
  /**************************************/
require('../config.inc.php');
include('./header.inc.php');

$keyword = $_POST['keyword'];
$term = $_POST['term'];
if (!$keyword)
{
echo '<script>alert(\'请输入关键字！\');window.history.back();</script>';
exit();
}
if($term=="topic"){
$sql = "select * from forum_topic where topic like '%".$keyword."%'";
$result = mysqli_query($sql);
}
elseif($term=="detail"){
$sql = "select * from forum_topic where detail like '%".$keyword."%'";
$result = mysqli_query($sql);
}
?>
<h3 class="center">搜索结果</h3>
<table>
<thead>
<tr class="teal darken-3">
<th width="31%">帖子</th>
<th width="15%">课程</th>
<th width="12%">访问</th>
<th width="12%">回复</th>
<th width="30%">发表日期</th>
</tr>
</thead>
<?php
//循环输出输出记录列表
while($rows=mysqli_fetch_array($result))
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
<a href="view_topic.php?id=<?php echo $rows['id'];?>"><?php echo $rows['topic']; ?></a><br/>
</td>
<td>
<?php
$sql1="SELECT * FROM forum_class WHERE id=".$rows['class_id']."";
$result1=mysqli_query($sql1);
$rows1=mysqli_fetch_array($result1);
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
</tr>
<?php
} //退出while循环
?>
</tbody>
</table>

<?php
include('./footer.inc.php')
?>