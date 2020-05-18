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
// [ 前台插件控制器基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\controller;

use think\App;
use think\Container;
use Thinker\basic\WeappController;
abstract class Weapp extends WeappController
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? Container::pull('app') : $app;
        parent::__construct($this->app);
        // 控制器初始化
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     * @return void
     * @throws HttpResponseException
     */
    protected function initialize()
    {
        parent::initialize();
    }
}