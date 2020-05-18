<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
<meta name="renderer" content="webkit">
<meta name="author" content="mengkun">
<meta name="generator" content="KodCloud">
<meta http-equiv="Cache-Control" content="no-siteapp">

<!-- 强制移动设备以app模式打开页面(即在移动设备下全屏，仅支持部分浏览器) -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-touch-fullscreen" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="full-screen" content="yes">
<!--UC强制全屏-->
<meta name="browsermode" content="application">
<!--UC应用模式-->
<meta name="x5-fullscreen" content="true">
<!--QQ强制全屏-->
<meta name="x5-page-mode" content="app">
<!--QQ应用模式-->

<title>{$title}</title>
<meta name="description" content="{$description}"/>
<meta name="keywords" content="{$keywords}"/>

<!-- 不支持IE8及以下版本浏览器 -->
<!--[if lte IE 8]>
        <script>window.location.href="{$domain}"</script>
<![endif]-->
<script>
    var YROnlinePlayer = "{$apiurl}";
</script>
{load: file = "/static/music/images/favicon.ico" /}
{load: file = "/static/music/js/jquery.min.js" /}
<!-- 播放器样式表文件 -->
{load: file = "/static/music/css/player.css" /}
<!-- 小屏幕样式修复 -->
{load: file = "/static/music/css/small.css" /}
<!-- 滚动条美化样式文件 -->
{load: file = "/static/music/css/jquery.mCustomScrollbar.min.css" /}
</head>
<body>
<div id="blur-img"></div>

<!-- header -->
<div class="header">
  <div class="logo" title="Version 2.4; Based on Meting; Powered by Mengkun"><a class="btn" href="{$domain}">{$header_logo}</a></div>
</div>
</div>
<!-- 中间主体区域 -->
<div class="center">
  <div class="container">
    <div class="btn-bar"> 
      <!-- tab按钮区 -->
      <div class="btn-box" id="btn-area"> <span class="btn" data-action="player" hidden>播放器</span> <span class="btn" data-action="playing" title="正在播放列表">正在播放</span> <span class="btn" data-action="sheet" title="音乐播放列表">播放列表</span> <span class="btn" data-action="search" title="点击搜索音乐">歌曲搜索</span> </div>
    </div>
    <!--class="btn-bar"-->
    
    <div class="data-area"> 
      <!--歌曲歌单-->
      <div id="sheet" class="data-box" hidden></div>
      
      <!--音乐播放列表-->
      <div id="main-list" class="music-list data-box"></div>
    </div>
    <!--class="data-area"--> 
    
    <!-- 右侧封面及歌词展示 -->
    <div class="player" id="player"> 
      <!--歌曲封面-->
      <div class="cover"> <img src="{$domain}/static/music/images/player_cover.png" class="music-cover" id="music-cover"> </div>
      <!--滚动歌词-->
      <div class="lyric">
        <ul id="lyric">
        </ul>
      </div>
      <div id="music-info" title="点击查看歌曲信息"></div>
    </div>
  </div>
  <!--class="container"--> 
</div>
<!--class="center"--> 

<!-- 播放器底部区域 -->
<div class="footer">
  <div class="container">
    <div class="con-btn"> <a href="javascript:;" class="player-btn btn-prev" title="上一首"></a> <a href="javascript:;" class="player-btn btn-play" title="暂停/继续"></a> <a href="javascript:;" class="player-btn btn-next" title="下一首"></a> <a href="javascript:;" class="player-btn btn-order" title="循环控制"></a> </div>
    <!--class="con-btn"-->
    
    <div class="vol">
      <div class="quiet"> <a href="javascript:;" class="player-btn btn-quiet" title="静音"></a> </div>
      <div class="volume">
        <div class="volume-box">
          <div id="volume-progress" class="mkpgb-area"></div>
        </div>
      </div>
    </div>
    <!--class="footer"-->
    
    <div class="progress">
      <div class="progress-box">
        <div id="music-progress" class="mkpgb-area"></div>
      </div>
    </div>
    <!--class="progress"--> 
  </div>
  <!--class="container"--> 
</div>
<!--class="footer"--> 
<!-- layer弹窗插件 --> 
{load: file = "/static/music/js/layer/layer.js" /} 
<!-- 播放器数据加载模块 --> 
{load: file = "/static/music/js/ajax.js" /} 
<!-- 播放器歌词解析模块 --> 
{load: file = "/static/music/js/lyric.js" /} 
<!-- 音乐列表配置 --> 
<script type="text/javascript" src="{$domain}/static/music/js/musicList.js{$version}"></script> 
<!-- 封装函数及ui交互模块 --> 
{load: file = "/static/music/js/functions.js" /} 
<!-- 播放器主体功能模块 --> 
{load: file = "/static/music/js/player.js" /} 
<!-- 滚动条美化插件 --> 
{load: file = "/static/music/js/jquery.mCustomScrollbar.concat.min.js" /} 
<!-- 背景模糊化插件 --> 
{load: file = "/static/music/js/background-blur.min.js" /} <span style="display: none"> </span>
</body>
</html>