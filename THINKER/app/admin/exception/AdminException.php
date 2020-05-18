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
// [ 后台异常处理 ]
// --------------------------------------------------------------------------
namespace app\admin\exception;

use Thinker\ExceptionHandle;
use think\exception\ValidateException;
use think\Response;
use Throwable;
use think\exception\HttpResponseException;
class AdminException extends ExceptionHandle
{

    public function render($request, Throwable $e): Response
    {
		/*$this->isJson = $this->getResponseType($request);
        // 参数验证错误
        if ($e instanceof ValidateException) {
            return $this->app->json->make(422, $e->getError());
        }
		// 请求异常
        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        }
        if ($e instanceof \Exception && $this->isJson) {
            return $this->app->json->fail(['code' => $e->getCode(), 'message' => $e->getMessage()]);
        }*/

        return parent::render($request, $e);
    }
	
}