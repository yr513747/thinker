<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=0">
<meta name="renderer" content="webkit">
<meta http-equiv="Cache-Control" content="no-siteapp">
<title>网易云音乐问题</title>
<link rel="shortcut icon" href="favicon.ico">
</head>
<body>
<div class="repository-content ">
  <div id="wiki-wrapper" class="page">
    <div class="d-flex flex-column flex-md-row gh-header">
      <h1 class="flex-auto min-width-0 mb-2 mb-md-0 mr-0 mr-md-2 gh-header-title"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">网易云音乐问题</font></font></h1>
      <div class="mt-0 mt-lg-1 flex-shrink-0 gh-header-actions"> </div>
    </div>
    <div class="mt-0 mt-lg-1 flex-shrink-0 gh-header-actions"> <a href="{$domain}" class="d-md-none "><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">返回音乐台</font></font></a> </div>
    <div id="wiki-content" class="mt-4">
      <div class="gutter-condensed gutter-lg d-flex flex-column flex-md-row">
        <div class="flex-shrink-0 col-12 col-md-9 mb-3 mb-md-0">
          <div id="wiki-body" class="gollum-markdown-content">
            <div class="markdown-body">
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">由于网易云音乐多次封禁Meting所使用的Cookie（具体表现为网易云音乐歌曲无法播放，播放列表仅剩一首歌）因此请自行获取Cookie并进行配置，以保障正常使用。方法如下：</font></font></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">1，浏览器开隐身模式，访问</font></font><a href="http://music.163.com/" rel="nofollow"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">http://music.163.com/</font></font></a></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">2，按F12（或Ctrl + shift + i）打开浏览器控制台，切换到</font></font><code>Network</code><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">选项卡，点亮该小红点</font></font></p>
              <p><img src="https://user-images.githubusercontent.com/16880885/36071015-91fba762-0f42-11e8-9733-0e0aa3289d8f.png" alt=""></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3，刷新浏览器，等待页面再一次加载完成</font></font></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4，将下侧出现的列表拉到最顶上，找到名称为music.163.com的这一项，点进去</font></font></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5，在</font></font><code>Headers</code><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">选项卡中往下拉，找到</font></font><code>Request Headers</code><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">，扩展并复制</font></font><code>Cookie：</code><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">后面的文字内容</font></font></p>
              <p><img src="https://user-images.githubusercontent.com/16880885/36071099-f567e06c-0f43-11e8-94ba-5bb226e530cb.png" alt=""></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">6，打开</font></font><code>music.php</code><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">配置文件，将获取到的Cookie粘贴到</font></font><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">指定位置</font></font><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> </font></font><code>！请使用专用的代码编辑器（如 notepad++）进行编辑！切勿使用记事本！切勿使用记事本！！切勿使用记事本！！！</code></p>
              <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">7，大功告成。。。如果过几天又失效了，请尝试再次获取Cookie。。。</font></font></p>
              <p><code>如果获取了 Cookie 还是无效，请尝试登录网易云账号再进行获取……（推荐使用小号，你懂的~）</code></p>
              <h2> <a id="user-content-参考资料" class="anchor" href="#" aria-hidden="true"><svg class="octicon octicon-link" viewBox="0 0 16 16" version="1.1" width="16" height="16" aria-hidden="true">
                <path fill-rule="evenodd" d="M4 9h1v1H4c-1.5 0-3-1.69-3-3.5S2.55 3 4 3h4c1.45 0 3 1.69 3 3.5 0 1.41-.91 2.72-2 3.25V8.59c.58-.45 1-1.27 1-2.09C10 5.22 8.98 4 8 4H4c-.98 0-2 1.22-2 2.5S3 9 4 9zm9-3h-1v1h1c1 0 2 1.22 2 2.5S13.98 12 13 12H9c-.98 0-2-1.22-2-2.5 0-.83.42-1.64 1-2.09V6.25c-1.09.53-2 1.84-2 3.25C6 11.31 7.55 13 9 13h4c1.45 0 3-1.69 3-3.5S14.5 6 13 6z"></path>
                </svg></a><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">参考资料：</font></font></h2>
              <p><a href="https://github.com/metowolf/Meting/wiki/special-for-netease"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">https://github.com/metowolf/Meting/wiki/special-for-netease</font></font></a></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</body>
</html>