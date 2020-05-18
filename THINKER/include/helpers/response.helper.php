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
// [ 系统核心函数存放文件 ]
// --------------------------------------------------------------------------
defined('DEBUG_LEVEL') || exit;
use think\Response;
use think\response\File;
use think\response\Json;
use think\response\Jsonp;
use think\response\Redirect;
use think\response\View;
use think\response\Xml;
use think\response\Html;
use think\response\Image;
// --------------------------------------------------------------------------
if (!function_exists('response')) {
    /**
     * 创建普通 Response 对象实例
     * @param mixed      $data   输出数据
     * @param int|string $code   状态码
     * @param array      $header 头信息
     * @param string     $type
     * @return Response
     */
    function response($data = '', $code = 200, $header = [], $type = 'html'): Response
    {
        return Response::create($data, $type, $code)->header($header);
    }
}

if (!function_exists('getResponse')) {
    /**
     * 获取 response 对象
     * @access protected
     * @param string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param string $type   返回数据格式
     * @return void
     */
    function getResponse($data = '', $type = '', $code = 200, $header = [], $options = [])
    {
        $type = $type ? $type : getResponseType();
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
}

if (!function_exists('download')) {
    /**
     * 获取\think\response\Download对象实例
     * @param string $filename 要下载的文件
     * @param string $name     显示文件名
     * @param bool   $content  是否为内容
     * @param int    $expire   有效期（秒）
     * @return \think\response\File
     */
    function download(string $filename, string $name = '', bool $content = false, int $expire = 180): File
    {
        return Response::create($filename, 'file')->name($name)->isContent($content)->expire($expire);
    }
}

if (!function_exists('json')) {
    /**
     * 获取\think\response\Json对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Json
     */
    function json($data = [], $code = 200, $header = [], $options = []): Json
    {
        return Response::create($data, 'json', $code)->header($header)->options($options);
    }
}

if (!function_exists('jsonp')) {
    /**
     * 获取\think\response\Jsonp对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Jsonp
     */
    function jsonp($data = [], $code = 200, $header = [], $options = []): Jsonp
    {
        return Response::create($data, 'jsonp', $code)->header($header)->options($options);
    }
}

if (!function_exists('redirect')) {
    /**
     * 获取\think\response\Redirect对象实例
     * @param string $url  重定向地址
     * @param int    $code 状态码
     * @return \think\response\Redirect
     */
    function redirect($url, $params = [], $code = 302, $with = []): Redirect
    {
		if (is_integer($params)) {
            $code   = $params;
            $params = [];
        }
		is_object($url) && $url = $url->build();
		if (strpos($url, '://') || (0 === strpos($url, '/') && empty($params))) {
            $redirect = $url;
        } else {
            $redirect = url($url, $params);
        }
        return Response::create($redirect, 'redirect', $code)->with($with);
    }
}

if (!function_exists('view')) {
    /**
     * 渲染模板输出
     * @param string   $template 模板文件
     * @param array    $vars     模板变量
     * @param int      $code     状态码
     * @param callable $filter   内容过滤
     * @return \think\response\View
     */
    function view(string $template = '', $vars = [], $code = 200, $filter = null): View
    {
        return Response::create($template, 'view', $code)->assign($vars)->filter($filter);
    }
}

if (!function_exists('display')) {
    /**
     * 渲染模板输出
     * @param string   $content 渲染内容
     * @param array    $vars    模板变量
     * @param int      $code    状态码
     * @param callable $filter  内容过滤
     * @return \think\response\View
     */
    function display(string $content, $vars = [], $code = 200, $filter = null): View
    {
        return Response::create($content, 'view', $code)->isContent(true)->assign($vars)->filter($filter);
    }
}

if (!function_exists('xml')) {
    /**
     * 获取\think\response\Xml对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Xml
     */
    function xml($data = [], $code = 200, $header = [], $options = []): Xml
    {
        return Response::create($data, 'xml', $code)->header($header)->options($options);
    }
}

if (!function_exists('html')) {
    /**
     * 获取\think\response\Html对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Html
     */
    function html($data = '', $code = 200, $header = [], $options = [])
    {
        return Response::create($data, 'html', $code)->header($header)->options($options);
    }
}

if (!function_exists('img')) {
    /**
     * 获取\think\response\Image对象实例
     * @param mixed $data    返回的数据
     * @param int   $code    状态码
     * @param array $header  头部
     * @param array $options 参数
     * @return \think\response\Image
     */
    function img($data = '', $code = 200, $header = [], $options = [], $contentType = 'image/png')
    {
        return Response::create($data, 'image', $code)->header($header)->contentType($contentType)->options($options);
    }
}
