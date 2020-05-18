<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://zjzit.cn>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace Thinker;

use think\App;
use think\console\Output as ConsoleOutput;
use think\exception\ErrorException;
use think\exception\Handle;
use Throwable;
use think\initializer\Error as BaseError;
/**
 * 错误和异常处理，由于在think\initializer\Error的基础上进行了些许修改，需要清空think\initializer\Error的所有属性和方法
 */
class Error extends BaseError
{
    /** @var App */
    protected $app;

    /**
     * 注册异常处理
     * @access public
     * @param App $app
     * @return void
     */
    public function init(App $app)
    {
		defined('DEBUG_LEVEL') or define('DEBUG_LEVEL', false);
        $this->app = $app;
        error_reporting(E_ALL);
		// 屏蔽php服务器错误报告 by 仰融
		$web_exception = $this->app->config->get('app.web_exception', true);
		// 部署模式
		$appDebug      = $this->app->isDebug();
        if (DEBUG_LEVEL === false && ($web_exception === false || $appDebug === false)) {
            error_reporting(0);
        }
        set_error_handler([$this, 'appError']);
        set_exception_handler([$this, 'appException']);
        register_shutdown_function([$this, 'appShutdown']);
    }
	
    /**
     * Exception Handler
     * @access public
     * @param \Throwable $e
     */
    public function appException(Throwable $e): void
    {
        $handler = $this->getExceptionHandler();

        $handler->report($e);

        if ($this->app->runningInConsole()) {
            $handler->renderForConsole(new ConsoleOutput, $e);
        } else {
            $handler->render($this->app->request, $e)->send();
        }
    }

    /**
     * Error Handler
     * @access public
     * @param integer $errno   错误编号
     * @param string  $errstr  详细错误信息
     * @param string  $errfile 出错的文件
     * @param integer $errline 出错行号
     * @throws ErrorException
     */
    public function appError(int $errno, string $errstr, string $errfile = '', int $errline = 0): void
    {
        $exception = new ErrorException($errno, $errstr, $errfile, $errline);

        if (error_reporting() & $errno) {
            // 将错误信息托管至 think\exception\ErrorException
            throw $exception;
        }
    }

    /**
     * Shutdown Handler
     * @access public
     */
    public function appShutdown(): void
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            // 将错误信息托管至think\ErrorException
            $exception = new ErrorException($error['type'], $error['message'], $error['file'], $error['line']);

            $this->appException($exception);
        }
    }

    /**
     * 确定错误类型是否致命
     *
     * @access protected
     * @param int $type
     * @return bool
     */
    protected function isFatal(int $type): bool
    {
        return in_array($type, [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE]);
    }

    /**
     * Get an instance of the exception handler.
     *
     * @access protected
     * @return Handle
     */
    protected function getExceptionHandler()
    {
        return $this->app->make(Handle::class);
    }
}
