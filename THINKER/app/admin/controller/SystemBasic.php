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
// [ 后台系统公用一级基类，封装一些常用的方法 ]
// --------------------------------------------------------------------------
namespace app\admin\controller;

use think\App;
use think\Container;
use Thinker\basic\BaseController;

abstract class SystemBasic extends BaseController
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
	
    /**异常抛出
     * @param $name
     */
    protected function exception($msg = '无法打开页面')
    {
        $this->assign(compact('msg'));
        return $this->fetch('public/exception');
    }
	
	/**
     * 空操作，找不到页面
     * @access public
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __callb($method, $args)
    {
        return $this->fetch('public/404');
    }
}
