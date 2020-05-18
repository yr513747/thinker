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
namespace think\traits\controller;

use think\Response;
use think\exception\HttpResponseException;
trait JumpTrait
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;
    /**
     * @var \think\View 视图类实例
     */
    protected $view;
    /**
     * 操作成功跳转的快捷方法
     * @access protected
     * @param  mixed  $msg    提示信息
     * @param  string $url    跳转的 URL 地址
     * @param  mixed  $data   返回的数据
     * @param  int    $wait   跳转等待时间
     * @param  array  $header 发送的 Header 信息
     * @param  string  $target 窗口打开方式
     * @return void
     * @throws HttpResponseException
     */
    protected function success($msg = '', string $url = null, $data = '', int $wait = 1, array $header = [], string $target = '_self')
    {
        if (is_null($url) && !is_null($this->request->server('HTTP_REFERER'))) {
            $url = $this->request->server('HTTP_REFERER');
        } elseif (is_null($url)) {
            $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = $this->app->route->buildUrl($url);
        }
        $type = $this->getResponseType();
        $result = array();
        $result['code'] = 1;
        $result['msg'] = $msg;
        $result['data'] = $data;
        $result['url'] = $url;
        $result['wait'] = $wait;
        $result['target'] = $target;
        if ('html' == strtolower($type)) {
            $view = $this->app->config->get('app.jump_success_tmpl') ? $this->app->config->get('app.jump_success_tmpl') : __DIR__ . '/tpl/dispatch_jump.tpl';
            $result = $this->view->fetch($view, $result);
        }
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }
    /**
     * 操作错误跳转的快捷方法
     * @access protected
     * @param  mixed  $msg    提示信息
     * @param  string $url    跳转的 URL 地址
     * @param  mixed  $data   返回的数据
     * @param  int    $wait   跳转等待时间
     * @param  array  $header 发送的 Header 信息
     * @param  string  $target 窗口打开方式
     * @return void
     * @throws HttpResponseException
     */
    protected function error($msg = '', string $url = null, $data = '', int $wait = 5, array $header = [], string $target = '_self')
    {
        if (is_null($url)) {
            $url = $this->request->isAjax() ? '' : 'javascript:history.back(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = $this->app->route->buildUrl($url);
        }
        $type = $this->getResponseType();
        $result = array();
        $result['code'] = 0;
        $result['msg'] = $msg;
        $result['data'] = $data;
        $result['url'] = $url;
        $result['wait'] = $wait;
        $result['target'] = $target;
        if ('html' == strtolower($type)) {
            $view = $this->app->config->get('app.jump_error_tmpl') ? $this->app->config->get('app.jump_error_tmpl') : __DIR__ . '/tpl/dispatch_jump.tpl';
            $result = $this->view->fetch($view, $result);
        }
        $response = Response::create($result, $type)->header($header);
        throw new HttpResponseException($response);
    }
    /**
     * 短消息函数,可以在某个动作处理后友好的提示信息
     * @param  string  $msg      消息提示信息
     * @param  string  $url    跳转地址
     * @param  int     $onlymsg  仅显示信息
     * @param  int     $limittime  限制时间
     * @param  array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function showMsg(string $msg = '', string $url = null, int $onlymsg = 0, int $limittime = 0, array $header = [])
    {
        if (is_null($url)) {
            $url = 'javascript:history.go(-1);';
        } elseif ('' !== $url && !strpos($url, '://') && 0 !== strpos($url, '/')) {
            $url = (string) $this->app->route->buildUrl($url);
        }
        $htmlhead = "<html>\r\n<head>\r\n<title>提示信息</title>\r\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\r\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\">\r\n<meta name=\"renderer\" content=\"webkit\">\r\n<meta http-equiv=\"Cache-Control\" content=\"no-siteapp\" />";
        $htmlhead .= "<base target='_self'/>\r\n<style>div{line-height:160%;}</style></head>\r\n<body leftmargin='0' topmargin='0' bgcolor='#FFFFFF'>\r\n<center>\r\n<script>\r\n";
        $htmlfoot = "</script>\r\n</center>\r\n</body>\r\n</html>\r\n";
        $litime = $limittime == 0 ? 1000 : $limittime;
        $func = '';
        if ($url == '-1') {
            if ($limittime == 0) {
                $litime = 5000;
            }
            $url = "javascript:history.go(-1);";
        }
        if ($url == '' || $onlymsg == 1) {
            $msg = "<script>alert(\"" . str_replace("\"", "“", $msg) . "\");</script>";
        } else {
            //当网址为:close::objname 时, 关闭父框架的id=objname元素
            if (preg_match('/close::/', $url)) {
                $tgobj = trim(preg_replace('/close::/', '', $url));
                $url = 'javascript:;';
                $func .= "window.parent.document.getElementById('{$tgobj}').style.display='none';\r\n";
            }
            $func .= "      var pgo=0;\r\n      function JumpUrl(){\r\n        if(pgo==0){ location='{$url}'; pgo=1; }\r\n      }\r\n";
            $rmsg = $func;
            $rmsg .= "document.write(\"<style>body{background:#F6F6F6}.tips-box{margin-top:50px;padding:0;width:450px;border:10px solid #E8E8E8;background:#fff;color:#444;font-family:微软雅黑}.tips .title{margin:0 20px;padding:15px 0;border-bottom:1px dotted #DDD;text-align:left;font-size:15px}.tips .title p{padding-left:10px;height:18px;border-left:2px solid #268B26;font-weight:600;line-height:18px;margin: 0;}.tips .content{position:relative;padding:30px;height:120px;background:#fff;color:#666;font-size:15px}.tips .content p.tip{color:#999;font-size:1px}.tips .content a.go{display:block;margin:15px auto 0;padding:6px 10px;width:80px;border:1px solid #268B26;border-radius:3px;color:#268B26;text-decoration:blink;font-size:13px}.tips .content a:hover{background:#268B26;color:#fff}</style>\");\r\n;";
            $rmsg .= "document.write(\"<div class='tips tips-box'>";
            $rmsg .= "<div class='title'><p>提示信息</p></div>\");\r\n";
            $rmsg .= "document.write(\"<div class='content'>\");\r\n";
            $rmsg .= "document.write(\"" . str_replace("\"", "“", $msg) . "\");\r\n";
            $rmsg .= "document.write(\"";
            if ($onlymsg == 0) {
                if ($url != 'javascript:;' && $url != '') {
                    $rmsg .= "<a href='{$url}' class='go'>点击跳转</a>";
                    $rmsg .= "<br/></div>\");\r\n";
                    $rmsg .= "setTimeout('JumpUrl()',{$litime});";
                } else {
                    $rmsg .= "<br/></div>\");\r\n";
                }
            } else {
                $rmsg .= "<br/><br/></div>\");\r\n";
            }
            $msg = $htmlhead . $rmsg . $htmlfoot;
        }
        $response = Response::create($msg)->header($header);
        throw new HttpResponseException($response);
    }
    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param  mixed  $data   要返回的数据
     * @param  int    $code   返回的 code
     * @param  mixed  $msg    提示信息
     * @param  string $type   返回数据格式
     * @param  array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function result($data, int $code = 0, $msg = '', string $type = 'json', array $header = [], array $options = [])
    {
        $result = array();
        $result['code'] = $code;
        $result['msg'] = $msg;
        $result['time'] = $this->request->server('REQUEST_TIME');
        $result['data'] = $data;
        $response = Response::create($result, $type)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
    /**
     * 获取\think\response\Json对象实例
     * @access protected
     * @param  array  $data   要返回的数据
     * @param  array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function json(array $data = [], int $code = 200, array $header = [], array $options = [])
    {
        $response = Response::create($data, 'json', $code)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
    /**
     * 获取\think\response\Jsonp对象实例
     * @access protected
     * @param  array  $data   要返回的数据
     * @param  array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function jsonp(array $data = [], int $code = 200, array $header = [], array $options = [])
    {
        $response = Response::create($data, 'jsonp', $code)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
    /**
     * 获取\think\response\Xml对象实例
     * @access protected
     * @param  array  $data   要返回的数据
     * @param  array  $header 发送的 Header 信息
     * @return void
     * @throws HttpResponseException
     */
    protected function xml(array $data = [], int $code = 200, array $header = [], array $options = [])
    {
        $response = Response::create($data, 'xml', $code)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
    /**
     * 获取\think\response\Download对象实例
     * @param  string $filename 要下载的文件
     * @param  string $name     显示文件名
     * @param  bool   $content  是否为内容
     * @param  int    $expire   有效期（秒）
     * @return void
     * @throws HttpResponseException
     */
    protected function download(string $filename, string $name = '', bool $content = false, int $expire = 180)
    {
        $response = Response::create($filename, 'file')->name($name)->isContent($content)->expire($expire);
        throw new HttpResponseException($response);
    }
    /**
     * 返回封装后的 API 数据到客户端
     * @access protected
     * @param  string  $error  提示信息
     * @param  int  $result   要返回的状态码
     * @param  string  $redirect_to   返回的跳转地址
     * @param  int $code   发送的http状态码
     * @param  array  $header 发送的 Header 信息
     * @param  array  $options json输出参数
     * @return void
     * @throws HttpResponseException
     */
    protected function message(string $error = '', int $result = 0, string $redirect_to = '', int $code = 200, array $header = [], array $options = [])
    {
        if (empty($redirect_to)) {
            $res = array();
            $res['error'] = $error;
            $res['result'] = $result;
        } else {
            $res = array();
            $res['error'] = $error;
            $res['result'] = $result;
            $res['redirect_to'] = $redirect_to;
        }
        $response = Response::create($res, 'json', $code)->header($header)->options($options);
        throw new HttpResponseException($response);
    }
    /**
     * URL 重定向
     * @access protected
     * @param  string|obj    $url    跳转的 URL 表达式
     * @param  array|int $params 其它 URL 参数
     * @param  int       $code   http code
     * @param  array     $with   隐式传参
     * @return void
     * @throws HttpResponseException
     */
    protected function redirect($url, $params = [], $code = 302, $with = [])
    {
        if (is_integer($params)) {
            $code = $params;
            $params = [];
        }
        is_object($url) && ($url = $url->build());
        if (strpos($url, '://') || 0 === strpos($url, '/') && empty($params)) {
            $redirect = $url;
        } else {
            $redirect = $this->app->route->buildUrl($url, $params)->build();
        }
        $response = Response::create($redirect, 'redirect');
        $response->code($code)->with($with);
        throw new HttpResponseException($response);
    }
    /**
     * 发送数据到客户端
     * @param  mixed   $data    返回的数据
     * @param  integer $code    状态码
     * @param  array   $header 头部
     * @param  array   $options 参数
     * @return void
     * @throws HttpResponseException
     */
    protected function html($data = '', int $code = 200, array $header = [], array $options = [])
    {
        $type = $this->getResponseType();
        $response = $this->getResponse($data, $type, $code, $header, $options);
        throw new HttpResponseException($response);
    }
    /**
     * 中断输出
     * @param  string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param  string $type   返回数据格式
     * @param  array  $header  参数
     * @return void
     * @throws HttpResponseException
     */
    protected function tpDie($data = '', string $type = '', int $code = 200, array $header = [], array $options = [])
    {
        $type = $type ? $type : $this->getResponseType();
        $response = $this->getResponse($data, $type, $code, $header, $options);
        throw new HttpResponseException($response);
    }
    /**
     * 输出数据
     * @access protected
     * @param  string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param  string $type   返回数据格式
     * @return mixed
     */
    protected function sendData($data = '', string $type = '', int $code = 200, array $header = [], array $options = [])
    {
        $type = $type ? $type : $this->getResponseType();
        $response = $this->getResponse($data, $type, $code, $header, $options);
        return $response->send();
    }
    /**
     * 获取 response 对象
     * @access protected
     * @param  string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param  string $type   返回数据格式
     * @return Response
     */
    protected function getResponse($data = '', string $type = '', int $code = 200, array $header = [], array $options = []) : Response
    {
        $type = $type ? $type : $this->getResponseType();
        if (is_object($data)) {
            if (is_callable([$data, '__toString']) && 'html' == strtolower($type)) {
                $data = $data->__toString();
            } elseif (is_callable([$data, 'toArray']) && 'html' != strtolower($type)) {
                $data = $data->toArray();
            } elseif ($data instanceof Response) {
                $data = $data;
            } else {
                $data = get_object_vars($data);
            }
        }
        if ($data instanceof Response) {
            $response = $data;
        } elseif (null !== $data && is_string($data)) {
            $response = Response::create($data, 'html', $code)->header($header)->options($options);
        } elseif (is_array($data)) {
            if ('html' == strtolower($type)) {
                $type = 'json';
            }
            $response = Response::create($data, $type, $code)->header($header)->options($options);
        } else {
            $response = Response::create()->code(304);
        }
        return $response;
    }
    /**
     * 获取当前的 response 输出类型
     * @access  protected
     * @return  string
     */
    protected function getResponseType() : string
    {
        return $this->request->isJson() || $this->request->isAjax() ? 'json' : 'html';
    }
}