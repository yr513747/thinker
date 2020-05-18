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
namespace app;

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
	use \think\traits\app\ErrorPage;
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

    /**
     * 记录异常信息（包括日志或者其它方式记录）
     *
     * @access public
     * @param  Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        // 使用内置的方式记录异常日志
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @access public
     * @param \think\Request   $request
     * @param Throwable $e
     * @return Response
     */
    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
		$this->isJson = $this->getResponseType($request);	
		// 返回JSON数据格式到客户端 包含状态信息 [当url_common_param为false时是无法获取到$_GET的数据的，故使用Request来获取<xiaobo.sun@qq.com>]
        $var_jsonp_handler = input('callback', '');
        $this->handler = !empty($var_jsonp_handler) ? $var_jsonp_handler : input('jsonpReturn', '');	
		
        if (!in_array($this->app->http->getName(), $this->app->config->get('app.deny_multi_app_list', ['admin']))) {
			// 关闭网站
            $global = $this->app->config->get('global', []);
            if (isset($global['web_status']) && $global['web_status'] === true) {
                $options = array();
                $options['error_message'] = isset($global['error_message']) ? $global['error_message'] : '网站暂时关闭，维护中……';
                $options['bar'] = '';
                $options['tips'] = array();
				
				if (!$this->isJson) {
					return $response = Response::create($this->setErrorPage($options))->code($this->getCode($e));
                } else {  
				    return $response = $this->resMessageToResponse(['error_message' => $options['error_message']]);                
                }              
            }
        } 
		if ($e instanceof \PDOException) {			    
		   // 针对数据库异常		
			$error = $e->errorInfo;
            $code0 = $e->getCode();
            $code1 = isset($error[1]) ? $error[1] : 0;
            $code2 = isset($error[2]) ? $error[2] : '';
            // 提高错误提示的友好性 
            $errcode = "{$code0}:{$code1}";
            $mysqlcode = $this->app->config->get('error_code.mysql', []);
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
        }
		if ($e instanceof HttpResponseException) {
			// 响应异常
            return $e->getResponse();
        }
		if ($e instanceof ValidateException) {
			// 参数验证错误		
            return $this->resMessageToResponse($e->getError());
        }
		if ($e instanceof HttpException) {
			// 请求异常
            return $this->renderHttpException($e);
        } 

        // 其他错误
        return $this->convertExceptionToResponse($e);
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
        $data = $this->convertExceptionToArray($exception);
        extract($data);
		
        // 调试模式与运营模式的错误页面不同 by 仰融
        if ($this->app->isDebug()) {
            include $this->app->config->get('app.exception_tmpl') ?: $this->app->getThinkPath() . 'tpl/think_exception.tpl';
        } else {
            include $this->app->config->get('app.error_tmpl') ?: $this->app->getThinkPath() . 'tpl/think_exception.tpl';
        }
		
        return ob_get_clean();
       
    }
}
