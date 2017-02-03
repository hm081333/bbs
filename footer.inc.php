</div>
<!-- 正文内容结束 -->

<!-- 页脚信息 -->
<footer class="page-footer cyan darken-4">

<div class="row container"><!--row行，可用col-->
<div class="col s12 m4"><!--col定义总长度为12，字母代表设备，s为手机分辨率的百分之85，m为平板分辨率的百分之85，l为电脑分辨率的百分之70。此语句为：此div在电脑上浏览时宽度为分辨率宽度百分之70的y一半，在手机上浏览时宽度为分辨率宽度的百分之85-->
<h5 class="white-text">老子是页脚</h5>
<p class="white-text">恭喜NOTE7爆炸</p>
</div>

<div class="col s12 m4"><!--类似上面 offset为抵消-在电脑上浏览时距离左边物体两个单位-->
<h5 class="white-text">友情链接</h5>
<ul>
<li><a class="white-text" href="#!">暂时没有友情链接</a></li>
<li><a class="white-text" href="#!">您的IP地址：</a></li>
<li><a class="white-text" href="#!">
<?php
function GetIP() {
if ($_SERVER["HTTP_X_FORWARDED_FOR"])
$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
else if ($_SERVER["HTTP_CLIENT_IP"])
$ip = $_SERVER["HTTP_CLIENT_IP"];
else if ($_SERVER["REMOTE_ADDR"])
$ip = $_SERVER["REMOTE_ADDR"];
else if (getenv("HTTP_X_FORWARDED_FOR"))
$ip = getenv("HTTP_X_FORWARDED_FOR");
else if (getenv("HTTP_CLIENT_IP"))
$ip = getenv("HTTP_CLIENT_IP");
else if (getenv("REMOTE_ADDR"))
$ip = getenv("REMOTE_ADDR");
else
$ip = "Unknown";
if($ip='::1')
$ip = "127.0.0.1";
return $ip;
}
echo GetIP();
?>
</a></li>
<li><a class="white-text" href="#!">您的所在地：</a></li>
<li><a class="white-text" href="#!">
<?php
/*$json=file_get_contents('http://ip.taobao.com/service/getIpInfo.php?ip='.GetIP().'');
$arr=json_decode($json);
echo $arr->data->country;    //国家
echo $arr->data->area;    //区域
echo $arr->data->region;    //省份
echo $arr->data->city;    //城市
echo $arr->data->isp;    //运营商*/
?>
</a></li>
</ul>
</div>

<div class="col s12 m4">
<h5 class="white-text">联系我</h5>
<ul>
<li><a style="width: 127px;" class="white-text btn waves-effect waves-light" href="mailto:522751485@qq.com"><i class="tiny material-icons">mail</i>Email</a></li>
<li><a style="width: 127px;" class="white-text btn waves-effect waves-light" href="tencent://AddContact/?fromId=50&fromSubId=1&subcmd=all&uin=522751485">QQ</a></li>
</ul>
</div>
</div>

<div class="footer-copyright"><!--版权说明版面-->
<div class="container"><!--版权说明容器-->
© 2016 Copyright LYi-Ho, All rights reserved.
<a class="grey-text text-lighten-4 center" href="./admin/">后台</a>
<a class="grey-text text-lighten-4 right" href="https://design.google.com/icons/">material icons library</a>
</div>
</div>
</footer>

<a href="#!" class="cd-top"></a><!--返回顶部按钮-->

</body>
</html>