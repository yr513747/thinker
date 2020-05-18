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
// [ 应用服务类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker\serviceinit;

use Thinker\utils\Json;
use Thinker\musicplayer\Service as MusicPlayerService;
use Thinker\app\Service as MultiAppService;
class ThinkerService extends BaseService
{
    /**
     * 容器绑定
     * @var array
     */
    public $bind = [
        // TDO更多定义
        'json' => Json::class,
    ];
    /**
     * 额外的系统服务定义
     * @var array
     */
    protected $ServiceProviders = [
        // TDO更多定义
        MultiAppService::class,
        MusicPlayerService::class,
    ];
    /**
     * 服务注册
     * @access public
     * @return void
     */
    public function register()
    {
        // 设置请求应用对象
        $this->app->request->withApp($this->app);
        // 设置web管理对象
        $this->app->request->withHttp($this->app->http);
        // 额外的系统服务定义
        foreach ($this->ServiceProviders as $ServiceProvider) {
            if (class_exists($ServiceProvider)) {
                $this->app->register($ServiceProvider);
            }
        }
    }
    /**
     * 服务启动
     * @access public
     * @return void  
     */
    public function boot()
    {
    }
}