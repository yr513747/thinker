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
// [ 异常类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);

namespace core\exceptions;

use Throwable;
use RuntimeException;

class AuthException extends RuntimeException
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if (is_array($message)) {
            $errInfo = $message;
            $message = $errInfo[0] ?? '未知错误';
            $code = $errInfo[1] ?? 400;
        }
        parent::__construct($message, $code, $previous);
    }
}