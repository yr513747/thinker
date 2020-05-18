<?php
declare (strict_types = 1);

namespace app;

use think\Service;

/**
 * 应用服务类
 */
class AppService extends Service
{
    /**
     * 容器绑定
     * @var array
     */
    public $bind = [
        // TDO更多定义
        //'json' => Json::class,
    ];
    
    /**
     * 服务注册
     * @access public
     * @return void
     */
    public function register()
    {
        
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
