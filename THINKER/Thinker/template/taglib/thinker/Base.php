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
// [ Thinker标签库解析基类 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\App;
use think\Container;
use think\facade\Db;
use Thinker\basic\traits\CommonFuncTrait;
abstract class Base 
{
	use CommonFuncTrait;
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
     * 初始化过的标签.
     * @var array
     */
    protected static $initialized = [];
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app = null)
    {
        $this->app = is_null($app) ? Container::pull('app') : $app;
        $this->request = $this->app['request'];
        $this->http = $this->app['http'];
		// 获取系统全局变量
        $this->loadParams();
        // 获取请求变量
        $this->loadForm();	
        // 初始化操作
        $this->initialize();
    }
    /**
     * 初始化本类
     * @access private
     * @return void
     */
    private function initialize()
    {
        try {
            $this->loadGlobal();
            $this->loadConfig();
            $this->loadSystem();
			$this->loadUsers();        
        } catch (\PDOException $e) {           
        } catch (\Exception $e) {
            throw $e;
        }
        // 初始化标签
        static::$initialized[static::class] = true;
        $this->init();
    }
    /**
     * 标签的初始化操作
     * @access protected
     * @return void
     */
    protected function init()
    {
    }
    /**
     * 在typeid传值为目录名称的情况下，获取栏目ID
     * @access protected
     * @return string
     */
    protected function getTrueTypeid($dirname = '')
    {
        if (!empty($dirname) && strval($dirname) != strval(intval($dirname))) {
            $dirname = Db::name('arctype')->comment('在typeid传值为目录名称的情况下，获取栏目ID')->where('dirname', $dirname)->cache(true, CACHE_TIME, "arctype")->getField('id');
        }
        return $dirname;
    }
    /**
     * 获取栏目目录名称
     * @access protected
     * @return string
     */
    protected function getTrueDirname($typeid = '')
    {
        if (!empty($typeid) && intval($typeid)) {
            $typeid = Db::name('arctype')->comment('获取栏目目录名称')->where('id', $typeid)->cache(true, CACHE_TIME, "arctype")->getField('dirname');
        }
        return $typeid;
    }
}