<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="renderer" content="webkit">
<meta http-equiv="X-UA-Compatible" content="IE=Edge,chrome=1" >
<meta http-equiv="Content-Language" content="zh-cn"/>
<meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" name="viewport" />
<title>{$steps[2]}_{$title}_{$powered}</title>
<link rel="stylesheet" href="{__INSTALL_PATH__}/css/install.css?v=v1.3.1" />
<script src="{__INSTALL_PATH__}/js/jquery.js?v=v1.3.1"></script> 
<style type="text/css">
.btn_a {
	width: 58px;
}
#table td {
	text-align: center;
}
#table td.first {
	text-align: left;
}
</style>
{:token_meta('__token__')}
</head>
<body>
<div class="wrap"> {include file="header"}
  <section class="section">
    <div class="blank30"></div>
    <div class="go go2"></div>
    <div class="blank30"></div>
    <div class="server">
      <table width="100%" id="table" cellspacing="1">
        <tr>
          <td class="td1">环境检测</td>
          <td class="td1" width="23%">推荐配置</td>
          <td class="td1" width="46%">当前状态</td>
        </tr>
        <tr>
          <td class="first">服务器环境</td>
          <td>IIS/apache2.0以上/nginx1.6以上</td>
          <td>{$server}</td>
        </tr>
        <tr>
          <td class="first">PHP版本</td>
          <td>5.4及5.4以上<br/>
            (支持php7)</td>
          <td>{$phpv_str}</td>
        </tr>
        <tr>
          <td class="first">safe_mode</td>
          <td><font title="影响缓存清除、系统升级、模板管理等功能">基础配置</font></td>
          <td>{$safe_mode}</td>
        </tr>
        <tr>
          <td class="first">GD库</td>
          <td><font title="影响验证码是否显示、图片水印、以及图像处理等问题">必须开启</font></td>
          <td>{$gd}</td>
        </tr>
        <tr>
          <td class="first">pdo</td>
          <td><font title="影响数据库的连接和一系列读、写、删、改操作">必须开启</font></td>
          <td>{$pdo}</td>
        </tr>
        <tr>
          <td class="first">pdo_mysql</td>
          <td><font title="影响数据库的连接和一系列读、写、删、改操作">必须开启</font></td>
          <td>{$pdo_mysql}</td>
        </tr>
      </table>
      <table width="100%" id="table" cellspacing="1">
        <tr>
          <td class="td1">函数检测</td>
          <td class="td1" width="23%">推荐配置</td>
          <td class="td1" width="46%">是否通过</td>
        </tr>
        <tr>
          <td class="first">curl_init</td>
          <td><font title="影响插件功能、伪静态、系统升级、采集文章等功能">必须扩展</font></td>
          <td>{$curl}</td>
        </tr>
      </table>
      <table width="100%" id="table" cellspacing="1">
        <tr>
          <td class="td1">目录、文件权限检查</td>
          <td class="td1" width="23%">推荐配置</td>
          <td class="td1" width="46%">是否通过</td>
        </tr>
        {foreach $res as $dir=>$w }
        <tr>
          <td class="first">{$dir}</td>
          <td>读写</td>
          <td>{$w}</td>
        </tr>
        {/foreach}
      </table>
    </div>
    <div class="bottom tac">
      <div class="blank20"></div>
      <center>
        <a href="{:url("install/Index/step2")}" class="btn_b">重新检测</a> {if ($err>0)} <a id="next_submit" href="javascript:void(0)" onClick="javascript:layer.alert('安装环境检测未通过，请检查', {icon: 5, title: false})" class="btn_a" style="background: gray;">下一步</a> {else /} <a id="next_submit" href="{:url("install/Index/step3")}" class="btn_a">下一步</a> {/if}
      </center>
    </div>
  </section>
</div>
<div class="blank20"></div>
{include file="footer"} 

<script src="{__INSTALL_PATH__}/js/layer-v3.1.1/layer/layer.js?v=v1.3.1"></script> 
<script type="text/javascript">
  $(function(){
    $('#next_submit').focus();
  });
</script>
</body>
</html>