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
// [ 系统公用一级基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace think;

use think\exception\ValidateException;
use think\exception\HttpException;
abstract class AbstractCommon
{
    use \think\traits\controller\ViewTrait;
    use \think\traits\controller\JumpTrait;
    use \think\traits\controller\AdminFuncTrait;
    use \think\traits\controller\CommonFuncTrait;
    /**
     * 当前应用实例
     * @var \think\Http
     */
    protected $http;
    /**
     * 是否批量验证
     * @var bool
     */
    protected $batchValidate = false;
    /**
     * 控制器中间件
     * @var array
     */
    protected $middleware = [];
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->request = $this->app->request;
        $this->http = $this->app->http;
        // 获取系统全局变量
        $this->loadParams();
    }
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        try {
            $this->loadGlobal();
            $this->loadConfig();
            $this->loadSystem();
            $this->loadUsers();
            // 系统变量赋值，可以在模板中使用$params获取系统变量支持无限级使用'.'号分割
            $params = $this->params;
            $version = $params['version'];
            $global = $params['global'];
            // 当前模板全局变量
            $this->thinker['global'] = $params['global'];
            // 判断是否开启注册入口
            $users_open_register = isset($params['users']['users_open_register']) ? $params['users']['users_open_register'] : '';
            unset($params['global']);
            $this->assign(compact('params', 'version', 'global', 'users_open_register'));
        } catch (\PDOException $e) {
            // 系统变量赋值
            $params = $this->params;
            $version = $params['version'];
            $this->assign(compact('params', 'version'));
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * 验证数据
     * @access protected
     * @param  array        $data     数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array        $message  提示信息
     * @param  bool         $batch    是否批量验证
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate(array $data, $validate, array $message = [], bool $batch = false)
    {
        if (is_array($validate)) {
            $v = new Validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                [$validate, $scene] = explode('.', $validate);
            }
            $class = false !== strpos($validate, '\\') ? $validate : $this->app->parseClass('validate', $validate);
            $v = new $class();
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        $v->message($message);
        // 是否批量验证
        if ($batch || $this->batchValidate) {
            $v->batch(true);
        }
        return $v->failException(true)->check($data);
    }
    /**
     * 空操作
     * @access public
     * @param  string  $method
     * @param  array  $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($this->app->isDebug()) {
			throw new HttpException(404, sprintf('method not exists:%s->%s()', static::class, $method));
        }
        throw new HttpException(404, 'Route Not Found');
    }
    // +-------------------------------------------------------------------------
    // | AdminFuncTrait
    // +-------------------------------------------------------------------------
    // +-------------------------------------------------------------------------
    // | CommonFuncTrait
    // +-------------------------------------------------------------------------
    // +-------------------------------------------------------------------------
    // | JumpTrait
    // +-------------------------------------------------------------------------
    // +-------------------------------------------------------------------------
    // | ViewTrait
    // +-------------------------------------------------------------------------
    // +-------------------------------------------------------------------------
    // | WeappTrait
    // +-------------------------------------------------------------------------
}