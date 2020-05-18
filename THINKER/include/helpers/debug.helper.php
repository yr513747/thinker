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
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\facade\Log;
// --------------------------------------------------------------------------
if (file_exists(root_path('vendor') . 'symfony' . DS . 'var-dumper' . DS . 'Resources' . DS . 'functions' . DS . 'dump.php')) {
    require_once root_path('vendor') . 'symfony' . DS . 'var-dumper' . DS . 'Resources' . DS . 'functions' . DS . 'dump.php';
}
// --------------------------------------------------------------------------
if (!function_exists('trace')) {
    /**
     * 记录日志信息
     * @param mixed  $log   log信息 支持字符串和数组
     * @param string $level 日志级别
     * @return array|void
     */
    function trace($log = '[think]', string $level = 'log')
    {
        if ('[think]' === $log) {
            return Log::getLog();
        }

        Log::record($log, $level);
    }
}

if (!function_exists('abort')) {
    /**
     * 抛出HTTP异常
     * @param integer|Response $code    状态码 或者 Response对象实例
     * @param string           $message 错误信息
     * @param array            $header  参数
     */
    function abort($code, string $message = '', array $header = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        } else {
            throw new HttpException($code, $message, null, $header);
        }
    }
}

if (!function_exists('exception')) {
    /**
     * 抛出异常处理
     *
     * @param string    $msg  异常消息
     * @param integer   $code 异常代码 默认为0
     * @param string    $exception 异常类
     *
     * @throws Exception
     */
    function exception($msg, $code = 0, $exception = '')
    {
        $e = $exception ?: '\\think\\Exception';
        throw new $e($msg, $code);
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed $vars 要输出的变量
     * @return void
     */
    function dump(...$vars)
    {
        ob_start();
        var_dump(...$vars);
        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }
        echo $output;
    }
}

if (!function_exists('halt')) {
    /**
     * 调试变量并且中断输出
     * @param mixed $vars 调试变量或者信息
     */
    function halt(...$vars)
    {
        dump(...$vars);
        throw new HttpResponseException(Response::create());
    }
}

if (!function_exists('tp_die')) {
    /**
     * 中断输出
     * @param string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param string $type   返回数据格式
     * @param array  $header  参数
     */
    function tp_die($data = '', $type = '', $code = 200, $header = [], $options = [])
    {      
        $type = $type ? $type : getResponseType();
        $response = getResponse($data, $type, $code, $header, $options);
        throw new HttpResponseException($response);
    }
}

if (!function_exists('sendData')) {
    /**
     * 输出数据
     * @access protected
     * @param string|array|object|Response $data 要输出的数据，支持模型或数据库数据集对象或者 Response对象实例
     * @param string $type   返回数据格式
     * @return void
     */
    function sendData($data = '', $type = '', $code = 200, $header = [], $options = [])
    {
		$type = $type ? $type : getResponseType();
        $response = getResponse($data, $type, $code, $header, $options);
        return $response->send();
    }
}