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
// [ 扩展路由规则，路由定义必须为数组 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
use think\facade\Route;
$route = [
    // 示例
    Route::group(function () {
        Route::rule('think$', function () {
            return 'hello,ThinkPHP6!';
        });
    })->cache(true),
];
return $route;