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
namespace think\exception;

class SystemException extends \RuntimeException 
{
	
    public function __construct($message = null, $code = 0, \Throwable $previous = null)
    {
		$this->path = $path;
        if (is_object($message)) {
            $code = $message->getCode();
            $previous = is_null($previous) ? $message : $previous;
            $message = $message->getMessage();
        }
        if (is_array($message)) {
            $errInfo = $message;
            $message = isset($errInfo[0]) ? $errInfo[0] : '未知错误';
            $code = isset($errInfo[1]) ? $errInfo[1] : 400;
        }
        parent::__construct((string) $message, (int) $code, $previous);
    }
}