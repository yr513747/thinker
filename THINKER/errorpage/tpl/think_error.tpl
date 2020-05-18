<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars_decode(nl2br(htmlentities($message))); ?></title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
<style type="text/css">
<!--
body {
	color: #444444;
	background-color: #EEEEEE;
	font-family: 'Trebuchet MS', sans-serif;
	font-size: 80%;
}
h1 {
}
h2 {
	font-size: 1.2em;
}
#page {
	background-color: #FFFFFF;
	width: 60%;
	margin: 24px auto;
	padding: 12px;
}
#header {
	padding: 6px;
	text-align: center;
}
.status3xx {
	background-color: #475076;
	color: #FFFFFF;
}
.status4xx {
	background-color: #C55042;
	color: #FFFFFF;
}
.status5xx {
	background-color: #F2E81A;
	color: #000000;
}
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
-->
</style>
</head>
<body>
<div id="page">
  <div id="header" class="status5xx">
    <h1><?php echo htmlspecialchars_decode(nl2br(htmlentities($message))); ?></h1>
  </div>
  <div id="content">
    <h2>发生以下错误：</h2>
    <p>由于服务器临时过载或维护，该服务目前不可用，请稍后再试。</p>
    <P>如有任何疑问，请与网站管理员联系。</p>
  </div>
  <div id="footer">
    <p>Powered by <a href="<?php echo htmlspecialchars_decode(nl2br(htmlentities($domain))); ?>/" rel="nofollow"><?php echo htmlspecialchars_decode(nl2br(htmlentities($host))); ?></a></p>
  </div>
</div>
</body>
</html>
