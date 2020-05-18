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
namespace Thinker\musicplayer;

use think\Service as BaseService;
use think\Route;
class Service extends BaseService
{
    public function boot()
    {
        $this->registerRoutes(function (Route $route) {
			$route->get("static/music/:path", "\\Thinker\\musicplayer\\PublicStatic@index")->denyExt('php')->completeMatch(false)->pattern(['path' => '[\w\.\/\-_]+'])->cache(false);
            $route->get('music$', "\\Thinker\\musicplayer\\MusicPlayer@index")->ext('')->cache(true);
            $route->any('MusicPlayer$', "\\Thinker\\musicplayer\\MusicPlayer@main")->ext('html')->cache(false);
        });
    }
}