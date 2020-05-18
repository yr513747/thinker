<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<meta http-equiv="Content-Language" content="zh-cn"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<title>{$steps[5]}_{$title}_{$powered}</title>
<link rel="stylesheet" href="{__INSTALL_PATH__}/css/install.css?v=v1.3.1" />
<script src="{__INSTALL_PATH__}/js/jquery.js?v=v1.3.1"></script> 
{:token_meta('__token__')}
</head>
<body>
<div class="wrap"> {include file="header"}
  <section class="section">
    <div class="blank10"></div>
    <div class="blank30"></div>
    <div class="go go4"></div>
    <div class="blank10"></div>
    <div class="blank30"></div>
    <div class="">
      <div class="result cc">
        <h1>恭喜您，Thinker已经成功安装完成！</h1>
        <h5>基于安全考虑，安装完成后即可将网站应用目录下的“install”文件夹删除！</h5>
      </div>
      <div class="bottom tac">
        <center>
          <a href="/" class="btn_b" style="color: #fff"> 访问网站首页 </a> <a id="next_submit" href="/admin.php" class="btn_a btn_submit J_install_btn"> 登陆网站后台 </a>
        </center>
      </div>
      <div class=""> </div>
    </div>
  </section>
</div>
<div class="blank30"></div>
{include file="footer"} 
<script>
$(function(){
    $.ajax({
        type: "POST",
        url: "{$service_thinker}",
        data: {domain:'{$domain}',last_domain:'{$host}',key_num:'{$cms_version}',install_time:'{$time}',serial_number:'{$mt_rand_str}',ip:'{$ip}',phpv:'{$phpv}',web_server:'{$web_server}'},
        dataType: 'jsonp',
        jsonp: "callback",
        success: function(){}
    });
    $('#next_submit').focus();
});
</script>
</body>
</html>