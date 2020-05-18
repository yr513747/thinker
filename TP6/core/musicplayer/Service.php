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
namespace core\musicplayer;

use think\Service as BaseService;
use think\Route;
class Service extends BaseService
{
    public function boot()
    {
        $baseConfigFile = __DIR__ . DIRECTORY_SEPARATOR . 'config.php';
        $upConfigFile = $this->app->getConfigPath() . 'music.php';
        if (!is_file($upConfigFile)) {
            copy($baseConfigFile, $upConfigFile);
        }
        $this->registerRoutes(function (Route $route) {
            $route->get('music$', "\\core\\musicplayer\\MusicPlayer@index");
			$route->get('music/doc$', "\\core\\musicplayer\\MusicPlayer@doc");
            $route->any('MusicPlayer$', "\\core\\musicplayer\\MusicPlayer@main");
        });
    }
}