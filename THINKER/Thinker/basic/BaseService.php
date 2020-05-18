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
// [ 服务层基类 ]
// --------------------------------------------------------------------------
namespace Thinker\basic;

use think\App;
use think\Container;
use app\common\model\Config as ConfigModel;
use Thinker\basic\traits\CommonFuncTrait;
abstract class BaseService
{
	use CommonFuncTrait;
    private $errorMsg;
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 当前应用实例
     * @var \think\Http
     */
    protected $http;
    /**
     * 请求对象
     * @var \think\Request
     */
    protected $request;	
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        global $_M;
        $this->app = is_null($app) ? Container::pull('app') : $app;
        $this->request = $this->app->request;
        $this->http = $this->app->http;
        // 控制器初始化
        $this->initialize();
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
		$this->loadParams();
		$this->loadGlobal();
    }
    /**
     * 设置错误信息
	 * @access public
     * @param string $errorMsg
     * @return $this
     */
    public function setErrorInfo($errorMsg = '操作失败,请稍候再试!')
    {
        $this->errorMsg = $errorMsg;
        return $this;
    }
    /**
     * 获取错误信息
	 * @access public
     * @param string $defaultMsg
     * @return string
     */
    public function getErrorInfo($defaultMsg = '操作失败,请稍候再试!')
    {
        return !empty($this->errorMsg) ? $this->errorMsg : $defaultMsg;
    }
}