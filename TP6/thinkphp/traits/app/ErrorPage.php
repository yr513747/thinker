<?php
// +-------------------------------------------------------------------------
// | THINKER [ Internet Ecological traffic aggregation and sharing platform ]
// +-------------------------------------------------------------------------
// | Copyright (c) 2019~2099 https://thinker.com All rights reserved.
// +-------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-------------------------------------------------------------------------
// | Author: yangrong <3223577520@qq.com>
// +-------------------------------------------------------------------------
declare (strict_types=1);
namespace think\traits\app;

use think\App;
trait ErrorPage
{
    /**
     * 设置错误信息
     * @access public
     * @param  array $options 配置参数
     * @return string
     */
    public function setErrorPage(array $options = array())
    {
        if (!isset($options['error_message'])) {
            $options['error_message'] = config('app.error_message', '页面错误！请稍后再试～');
        }
        if (!isset($options['bar'])) {
            $options['bar'] = '发生以下错误：';
        }
        if (!isset($options['tips'])) {
            $options['tips'] = array('请求的URL导致内部服务器错误。', '如果您反复收到此消息，请与网站管理员联系。');
        }
        $domain = request()->domain(true);
        $host = request()->host(true);
        $video = '';
        $videotpl = '<div id="audioBox" style="display:none;"></div>';
        if (is_file(root_path('public' . \DIRECTORY_SEPARATOR . 'static' . \DIRECTORY_SEPARATOR . 'music') . 'huaqiaoliushui.mp3')) {
            $video .= '"/static/music/huaqiaoliushui.mp3"' . ',';
        }
        if (is_file(root_path('public' . \DIRECTORY_SEPARATOR . 'static' . \DIRECTORY_SEPARATOR . 'music') . 'gxdygj.mp3')) {
            $video .= '"/static/music/gxdygj.mp3"' . ',';
        }
        if (!empty($video)) {
            $videotpl .= '
  <script type="text/javascript"> 
    window.onload = function(){ 
      var arr = [' . $video . '];               
      var myAudio = new Audio(); 
      myAudio.preload = true; 
      myAudio.controls = true; 
	  //每次读数组最后一个元素 
      myAudio.src = arr.pop();         
      myAudio.addEventListener("ended", playEndedHandler, false); 
      myAudio.play(); 
      document.getElementById("audioBox").appendChild(myAudio); 
	  //循环时无法触发ended事件 
      myAudio.loop = true;
      function playEndedHandler(){ 
        myAudio.src = arr.pop(); 
        myAudio.play(); 
        console.log(arr.length); 
		//只有一个元素时解除绑定 
        !arr.length && myAudio.removeEventListener("ended",playEndedHandler,false);
      } 
    } 
  </script>';
        }
        $data = array();
        foreach ($options['tips'] as $tips) {
            if ($tips) {
                $data[] = '<p>' . $tips . '</p>';
            }
        }
        $content = "<h2>{$options['bar']}</h2>" . implode('', $data);
        $html = <<<EOF
<!DOCTYPE html>
<html>
<head>
    <title>{$options['error_message']}</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="noindex" />
    <style type="text/css"><!--
    body {
        color: #444444;
        background-color: #EEEEEE;
        font-family: 'Trebuchet MS', sans-serif;
        font-size: 80%;
    }
    h1 {}
    h2 { font-size: 1.2em; }
    #page{
        background-color: #FFFFFF;
        width: 60%;
        margin: 24px auto;
        padding: 12px;
    }
    #header {
        padding: 6px ;
        text-align: center;
    }
    .status3xx { background-color: #475076; color: #FFFFFF; }
    .status4xx { background-color: #C55042; color: #FFFFFF; }
    .status5xx { background-color: #F2E81A; color: #000000; }
    #content {
        padding: 4px 0 24px 0;
    }
    #footer {
        color: #666666;
        background: #f9f9f9;
        padding: 10px 20px;
        border-top: 5px #efefef solid;
        font-size: 0.8em;
        text-align: center;
    }
    #footer a {
        color: #999999;
    }
    --></style>
</head>
<body>
    <div id="page">
        <div id="header" class="status5xx">
            <h1>{$options['error_message']}</h1>
        </div>
        <div id="content">{$content}</div>
        <div id="footer">
            <p>Powered by <a href="{$domain}" rel="nofollow">{$host}</a></p>
        </div>
    </div>
{$videotpl}
</body>
</html>
EOF;
        return $html;
    }
}