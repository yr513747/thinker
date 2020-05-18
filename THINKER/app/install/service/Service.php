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
namespace app\install\service;

use think\facade\Route;
class Service extends \think\Service
{
    public function register()
    {
		Route::get("install/:path", "\\app\\install\\PublicStatic@index")->denyExt('php|html|htm|shtml')->completeMatch(false)->pattern(['path' => '[\w\.\/\-_]+'])->cache(false);  
    }
}