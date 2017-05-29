<?php
header("Content-type: text/html; charset=utf-8");
  /**************************************/
  /*		文件名：user.php		    */
  /*		  功能：管理用户	    	 	*/
  /**************************************/

    require('../config.inc.php');

//判断用户是否登录，从而显示不同的界面
if (isset($_SESSION["admin"])&&$_SESSION['admin']) { //登陆后显示页面
//取得当前页数
  $page=isset($_GET["page"])?$_GET['page']:0;


  //每页最多显示的记录数
  $each_page = 8;

  //计算页面开始位置
  if (!$page || $page == 1) {
      $start = 0;
  } else {
      $offset = $page - 1;
      $start = ($offset * $each_page);
  } ?>
<?php include('../header/admin.header.inc.php'); ?>

<h3 class="center">管理课程分类</h3>
<fieldset>
<legend>Manage Class</legend>
<table>
<thead>
<th width="25%">课程</td>
<th>课程说明</th>
<th width="5%">操作</th>
</thead>
<tbody>
<?php
$sql="SELECT * FROM forum_class LIMIT $start, $each_page";
    $rows=fetch_all($sql);
//循环输出输出记录列表
foreach ($rows as $key => $row) {
    ?>
<form enctype="multipart/form-data" method="post" action="update_class.php">
<input name="id" type="hidden" value="<?php echo $row['id']; ?>">
<tr>
<td>
<input name="name<?php echo $row['id']; ?>" type="text" value="<?php echo $row['name']; ?>">
</td>
<td>
<input name="tips<?php echo $row['id']; ?>" type="text" value="<?php echo $row['tips']; ?>">
</td>
<td>
<button type="submit" name="submit" class="btn-floating waves-effect waves-light">修改</button>
</form>
<form enctype="multipart/form-data" method="post" action="del_class.php">
<input name="id" type="hidden" value="<?php echo $row['id']; ?>">
<button type="submit" name="submit" class="btn-floating waves-effect waves-light">删除</button>
</form>
</td>
</tr>
<?php

} //退出while循环
  $prevpage = 0;
  //计算前一页
  if ($page > 1) {
      $prevpage = $page - 1;
  }

  //当前记录
  $currentend = $start + $each_page;

  //取得所有的记录数
  $sql = "SELECT * FROM forum_class";
    $total = num_rows($sql);
    $nextpage = 0;
  //计算后一页
  if ($total>$currentend) {
      if (!$page) {
          $nextpage = 2;
      } else {
          $nextpage = $page + 1;
      }
  } ?>
<tr>
<td colspan="3">
<?php
//判断分页并输出
if ($prevpage || $nextpage) {
    //上一页
if ($prevpage) {
    ?>
<a class="btn waves-effect waves-light" href="./class.php?page=<?php echo $prevpage ?>"><i class="material-icons">arrow_back</i></a>
<?php

} else {
    ?>
<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_back</i></a>
<?php

}
//后一页
if ($nextpage) {
    ?>
<a class="btn waves-effect waves-light" href="./class.php?page=<?php echo $nextpage ?>"><i class="material-icons">arrow_forward</i></a>
<?php

} else {
    ?>
<a class="disabled btn waves-effect waves-light"><i class="material-icons">arrow_forward</i></a>
<?php

}
} ?>
</td>
</tr>
</tbody>
</table>

<?php

} else {//未登陆返回登陆页面
    header("Location: ./");
}

    //公用尾部页面
    include('../header/footer.inc.php');
?>
