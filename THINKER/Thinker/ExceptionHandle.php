<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker;

use Exception;
use think\App;
use think\console\Output;
use think\db\exception\DataNotFoundException;
use think\db\exception\ModelNotFoundException;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\Request;
use think\Response;
use Throwable;

/**
 * 应用异常处理类
 */
class ExceptionHandle extends Handle
{
    /** @var App */
    protected $app;
	/**
     * 不需要记录信息（日志）的异常类列表
     * @var array
     */

    protected $ignoreReport = [
        HttpException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        DataNotFoundException::class,
        ValidateException::class,
    ];
	
	/**
     * 是否为异步请求
     * @var bool
     */

    protected $isJson = false;
	/**
     * 返回JSON数据格式到客户端 包含状态信息
     * @var string
     */
	protected $handler = '';

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        if (!$this->isIgnoreReport($exception)) {
            // 收集异常数据
            if ($this->app->isDebug()) {
                $data = [
                    'file'    => $exception->getFile(),
                    'line'    => $exception->getLine(),
                    'message' => $this->getMessage($exception),
                    'code'    => $this->getCode($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}[{$data['file']}:{$data['line']}]";
            } else {
                $data = [
                    'code'    => $this->getCode($exception),
                    'message' => $this->getMessage($exception),
                ];
                $log = "[{$data['code']}]{$data['message']}";
            }

            if ($this->app->config->get('log.record_trace')) {
                $log .= PHP_EOL . $exception->getTraceAsString();
            }

            try {
                $this->app->log->record($log, 'error');
            } catch (Exception $e){}
        }
    }

    protected function isIgnoreReport(Throwable $exception): bool
    {
        foreach ($this->ignoreReport as $class) {
            if ($exception instanceof $class) {
                return true;
            }
        }

        return false;
    }

    /**
     * 将异常呈现到HTTP响应中
     *
     * @access public
     * @param Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
		
        $this->isJson = $this->getResponseType($request);	
		// 返回JSON数据格式到客户端 包含状态信息 [当url_common_param为false时是无法获取到$_GET的数据的，故使用Request来获取<xiaobo.sun@qq.com>]
        $var_jsonp_handler = input('callback', '');
        $this->handler = !empty($var_jsonp_handler) ? $var_jsonp_handler : input('jsonpReturn', '');	
		
        if (!in_array($this->app->http->getName(), $this->app->config->get('app.deny_multi_app_list', []))) {
			// 关闭网站
            $global = $this->app->config->get('global', []);
            if (isset($global['web_status']) && $global['web_status'] === true) {
                $options = array();
                $options['error_message'] = isset($global['error_message']) ? $global['error_message'] : '网站暂时关闭，维护中……';
                $options['bar'] = '';
                $options['tips'] = array();
				
				if (!$this->isJson) {
                    $this->app->setErrorPage($request, $options);
					return $response = Response::create()->code($this->getCode($e));
                } else {  
				    return $response = $this->resMessageToResponse(['error_message' => $options['error_message']]);                
                }              
            }
        } 
		/*if ($request->app() != 'install' && !$this->isJson) {
			// 程序未安装
            $install_path = base_path('install');
            $install_extra_path = $install_path . 'extra' . DS;
            if (is_dir($install_path) && !is_file($install_extra_path . "install.lock")) {
                return $response = redirect('install/Index/index');
            }
        } */
		if ($e instanceof \PDOException) {			    
		   // 针对数据库异常		
			$error = $e->errorInfo;
            $code0 = $e->getCode();
            $code1 = isset($error[1]) ? $error[1] : 0;
            $code2 = isset($error[2]) ? $error[2] : '';
            // 提高错误提示的友好性 
            $errcode = "{$code0}:{$code1}";
            $mysqlcode = config('error_code.mysql', []);
            $error_message = "";
            if (!empty($mysqlcode[$errcode])) {              
                $error_message = $mysqlcode[$errcode];
            }
            // --end
            $error_code = $e->getMessage();
			// 转化编码 
            $error_code = iconv('GB2312', 'UTF-8', $error_code); 
			if (!$this->isJson) {
			    return $this->convertExceptionToResponse($e);
            }   
			    
            return $this->resMessageToResponse(['error_message' => $error_message, 'error_code' => $error_code]);            
        } elseif ($e instanceof HttpResponseException) {
			// 响应异常
            return $e->getResponse();
        } elseif ($e instanceof ValidateException) {
			// 参数验证错误		
            return $this->resMessageToResponse($e->getError());
        } elseif ($e instanceof HttpException) {
			// 请求异常
            return $this->renderHttpException($e);
        } else {
			// 其他异常
            return $this->convertExceptionToResponse($e);
        }
    }
	
	/**
     * 获取当前的 response 输出类型
     * @access protected
     * @return string
     */
    public function getResponseType($request)
    {
        if ($request->isJson() || $request->isAjax()) {
            return true;
        }
        // 兼容 Cors Postman 请求
        if (false !== strpos($_SERVER['HTTP_USER_AGENT'], 'Postman') || isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'cors') {
            return true;
        }
        return false;
    }
	
	/**
	 * 返回JSON数据格式到客户端 包含状态信息 
     * @access protected
     * @param array $message
     * @return Response
     */
    protected function resMessageToResponse(array $message = []): Response
    {
        if ($this->handler) {
             $response = Response::create($message, 'jsonp', 422);
        } else {
             $response = Response::create($message, 'json', 422);
        }
		return $response;
    }

    /**
     * @access public
     * @param Output    $output
     * @param Throwable $e
     */
    public function renderForConsole(Output $output, Throwable $e): void
    {
        if ($this->app->isDebug()) {
            $output->setVerbosity(Output::VERBOSITY_DEBUG);
        }

        $output->renderException($e);
    }

    /**
     * @access protected
     * @param HttpException $e
     * @return Response
     */
    protected function renderHttpException(HttpException $e): Response
    {
        $status   = $e->getStatusCode();
        $template = $this->app->config->get('app.http_exception_template');

        if (!$this->app->isDebug() && !empty($template[$status])) {
            return Response::create($template[$status], 'view', $status)->assign(['e' => $e]);
        } else {
            return $this->convertExceptionToResponse($e);
        }
    }

    /**
     * 收集异常数据
     * @param Throwable $exception
     * @return array
     */
    protected function convertExceptionToArray(Throwable $exception): array
    {
        if ($this->app->isDebug()) {
            // 调试模式，获取详细的错误信息
            $traces = [];
            $nextException = $exception;
            do {
                $traces[] = [
                    'name'    => get_class($nextException),
                    'file'    => $nextException->getFile(),
                    'line'    => $nextException->getLine(),
                    'code'    => $this->getCode($nextException),
                    'message' => $this->getMessage($nextException),
                    'trace'   => $nextException->getTrace(),
                    'source'  => $this->getSourceCode($nextException),
                ];
            } while ($nextException = $nextException->getPrevious());
            $data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
				'domain' => $this->app->request->domain(),
                'host' => $this->app->request->host(),
                'traces'  => $traces,
                'datas'   => $this->getExtendData($exception),
                'tables'  => [
                    'GET Data'              => $this->app->request->get(),
                    'POST Data'             => $this->app->request->post(),
                    'Files'                 => $this->app->request->file(),
                    'Cookies'               => $this->app->request->cookie(),
                    'Session'               => $this->app->session->all(),
                    'Server/Request Data'   => $this->app->request->server(),
                    'Environment Variables' => $this->app->request->env(),
                    'ThinkPHP Constants'    => $this->getConst(),
                ],
            ];
        } else {
            // 部署模式仅显示 Code 和 Message
            $data = [
                'code'    => $this->getCode($exception),
                'message' => $this->getMessage($exception),
				'domain' => $this->app->request->domain(),
                'host' => $this->app->request->host(),
            ];

            if (!$this->app->config->get('app.show_error_msg')) {
                // 不显示详细错误信息
                $data['message'] = $this->app->config->get('app.error_message');
            }
        }

        return $data;
    }

    /**
     * @access protected
     * @param Throwable $exception
     * @return Response
     */
    protected function convertExceptionToResponse(Throwable $exception): Response
    {
        
		
		if (!$this->isJson) {
            $response = Response::create($this->renderExceptionContent($exception));
        } else {
            $response = $this->resMessageToResponse($this->convertExceptionToArray($exception));
        }

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $response->header($exception->getHeaders());
        }

        return $response->code($statusCode ?? 500);
    }

    protected function renderExceptionContent(Throwable $exception): string
    {
		ob_start();
        ob_implicit_flush(0);
        $data = $this->convertExceptionToArray($exception);
        extract($data);
        // 调试模式与运营模式的错误页面不同 by 仰融
	
        if ($this->app->isDebug()) {
            include $this->app->config->get('app.exception_tmpl') ?: $this->app->getThinkPath() . 'tpl/think_exception.tpl';
        } else {
            include $this->app->config->get('app.error_tmpl') ?: $this->app->getThinkPath() . 'tpl/think_exception.tpl';
        }
        // 获取并清空缓存
        //$content = ob_get_clean();
		$content = ob_get_contents();
        ob_end_clean();
        return $content;
       
    }

    /**
     * 获取错误编码
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param Throwable $exception
     * @return integer                错误编码
     */
    protected function getCode(Throwable $exception)
    {
        $code = $exception->getCode();

        if (!$code && $exception instanceof ErrorException) {
            $code = $exception->getSeverity();
        }

        return $code;
    }

    /**
     * 获取错误信息
     * ErrorException则使用错误级别作为错误编码
     * @access protected
     * @param Throwable $exception
     * @return string                错误信息
     */
    protected function getMessage(Throwable $exception): string
    {
        $message = $exception->getMessage();

        if ($this->app->runningInConsole()) {
            return $message;
        }

        $lang = $this->app->lang;

        if (strpos($message, ':')) {
            $name    = strstr($message, ':', true);
            $message = $lang->has($name) ? $lang->get($name) . strstr($message, ':') : $message;
        } elseif (strpos($message, ',')) {
            $name    = strstr($message, ',', true);
            $message = $lang->has($name) ? $lang->get($name) . ':' . substr(strstr($message, ','), 1) : $message;
        } elseif ($lang->has($message)) {
            $message = $lang->get($message);
        }

        return $message;
    }

    /**
     * 获取出错文件内容
     * 获取错误的前9行和后9行
     * @access protected
     * @param Throwable $exception
     * @return array                 错误文件内容
     */
    protected function getSourceCode(Throwable $exception): array
    {
        // 读取前9行和后9行
        $line  = $exception->getLine();
        $first = ($line - 9 > 0) ? $line - 9 : 1;

        try {
            $contents = file($exception->getFile()) ?: [];
            $source   = [
                'first'  => $first,
                'source' => array_slice($contents, $first - 1, 19),
            ];
        } catch (Exception $e) {
            $source = [];
        }

        return $source;
    }

    /**
     * 获取异常扩展信息
     * 用于非调试模式html返回类型显示
     * @access protected
     * @param Throwable $exception
     * @return array                 异常类定义的扩展数据
     */
    protected function getExtendData(Throwable $exception): array
    {
        $data = [];

        if ($exception instanceof \think\Exception) {
            $data = $exception->getData();
        }

        return $data;
    }

    /**
     * 获取常量列表
     * @access protected
     * @return array 常量列表
     */
    protected function getConst(): array
    {
        $const = get_defined_constants(true);

        return $const['user'] ?? [];
    }
}
