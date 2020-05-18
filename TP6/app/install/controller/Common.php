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
// [ 安装程序公共一级基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\install\controller;

use think\App;
use think\Validate;
use think\Response;
use think\exception\HttpResponseException;
use think\exception\ValidateException;
use think\exception\FileException;
use core\exceptions\AuthException;
use think\Util as FileService;
use think\traits\app\ErrorPage;
use think\AbstractController;
abstract class Common extends AbstractController
{
	use ErrorPage;
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
     * 程序根目录
     * @var string
     */
    protected $root_path;
    /**
     * 当前应用目录
     * @var string
     */
    protected $app_path;
      
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
		$this->root_path = $this->app->getRootPath();
        $this->app_path = $this->app->getAppPath();
        // 系统变量赋值，可以在模板中使用$params获取系统变量支持无限级使用'.'号分割
        $params = $this->params;
        $version = $params['version'];
        $this->assign('action', strtolower($params['action_name']));
        $this->assign(compact('params', 'version'));
        if (!is_file($this->root_path . 'public/static/install/js/common.js')) {
            $this->install(false);
        }
    }
    /**
     * 安装动作
     * @access protected
     * @param  bool $overWrite 模板文件或者模板规则
     * @return boolean
     */
    protected function install(bool $overWrite = true)
    {
        /**复制静态资源文件 */
        $oldstatic = app_path('static');
        $newstatic = root_path('public/static');
        if (FileService::copyPath($oldstatic, $newstatic, null, ['override' => $overWrite]) === false) {
            throw new AuthException('资源安装失败');
        }
    }
    /**
     * 加载环境变量
     * @access protected
     * @return void
     */
    protected function environmentFileinit() : void
    {
        $environmentFile = $this->root_path . '.env';
        if (!is_file($environmentFile)) {
            $example = $this->root_path . '.example.env';
            if (is_file($example)) {
                set_error_handler(function ($type, $msg) use(&$error) {
                    $error = $msg;
                });
                $renamed = copy($example, $environmentFile);
                restore_error_handler();
                if (!$renamed) {
                    throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $example, $environmentFile, strip_tags($error)));
                }
                @chmod((string) $environmentFile, 0755 & ~umask());
            } else {
                if (FileService::createFile($environmentFile, true) === false) {
                    throw new FileException(sprintf('Unable to create environment variable profile (%s)', $environmentFile));
                }
            }
        }
    }
    /**
     * 设置脚本运行超时时间
     * 0表示不限制，支持连贯操作
     * @param  integer  $time
     * @return  $this
     */
    protected function setTimeout($time = null)
    {
        if (!is_null($time)) {
            @set_time_limit($time) || ini_set("max_execution_time", $time);
        }
        return $this;
    }
    /**
     * 设置并抛出错误信息
     * @param  string|array $message 错误信息
     * @throws HttpResponseException
     */
    protected function setError($message)
    {
        $options = array();
        $options['error_message'] = '页面错误！请稍后再试～';
        $options['bar'] = '发生以下错误：';
        if (is_array($message)) {
            $options['tips'] = $message;
        } else {
            $options['tips'] = array($message);
        }
        throw new HttpResponseException(Response::create($this->setErrorPage($options)));
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
        $this->assign('msg', lang('method not exists') . ':' . $method . '()');
        return $this->fetch('public/error');
    }
    /**
     * 返回数据
     * @access protected
     * @param string $msg  提示信息
     * @param int    $code 状态码
     * @param mixed  $data 数据
     * @return array        
     */
    protected function dataReturn(string $msg = '', int $code = 0, $data = '')
    {
        $result = array('msg' => $msg, 'code' => $code, 'data' => $data);
        // ajax的时候，success和error错误由当前方法接收
        if ($this->request->isJson() || $this->request->isAjax()) {
            if (isset($msg['info'])) {
                // success模式下code=0, error模式下code参数-1
                $result = array('msg' => $msg['info'], 'code' => -1, 'data' => '');
            }
        }
        // 错误情况下，防止提示信息为空
        if ($result['code'] != 0 && empty($result['msg'])) {
            $result['msg'] = '操作失败';
        }
        return $result;
    }
    /**
     * 参数校验
     * @access protected
     * @param array $data 原始数据
     * @param array $params 校验数据
     * @return boolean|string 成功true, 失败 错误信息
     */
    protected function paramsChecked($data, $params)
    {
        if (empty($params) || !is_array($data) || !is_array($params)) {
            return '内部调用参数配置有误';
        }
        foreach ($params as $v) {
            if (empty($v['key_name']) || empty($v['error_msg'])) {
                return '内部调用参数配置有误';
            }
            // 是否需要验证
            $is_checked = true;
            // 数据或字段存在则验证
            // 1 数据存在则验证
            // 2 字段存在则验证
            if (isset($v['is_checked'])) {
                if ($v['is_checked'] == 1) {
                    if (empty($data[$v['key_name']])) {
                        $is_checked = false;
                    }
                } else {
                    if ($v['is_checked'] == 2) {
                        if (!isset($data[$v['key_name']])) {
                            $is_checked = false;
                        }
                    }
                }
            }
            // 是否需要验证
            if ($is_checked === false) {
                continue;
            }
            // 数据类型,默认字符串类型
            $data_type = empty($v['data_type']) ? 'string' : $v['data_type'];
            // 验证规则，默认isset
            $checked_type = isset($v['checked_type']) ? $v['checked_type'] : 'isset';
            switch ($checked_type) {
                // 是否存在
                case 'isset':
                    if (!isset($data[$v['key_name']])) {
                        return $v['error_msg'];
                    }
                    break;
                // 是否为空
                case 'empty':
                    if (empty($data[$v['key_name']])) {
                        return $v['error_msg'];
                    }
                    break;
                // 是否存在于验证数组中
                case 'in':
                    if (empty($v['checked_data']) || !is_array($v['checked_data'])) {
                        return '内部调用参数配置有误';
                    }
                    if (!isset($data[$v['key_name']]) || !in_array($data[$v['key_name']], $v['checked_data'])) {
                        return $v['error_msg'];
                    }
                    break;
                // 是否为数组
                case 'is_array':
                    if (!isset($data[$v['key_name']]) || !is_array($data[$v['key_name']])) {
                        return $v['error_msg'];
                    }
                    break;
                // 长度
                case 'length':
                    if (!isset($v['checked_data'])) {
                        return '长度规则值未定义';
                    }
                    if (!is_string($v['checked_data'])) {
                        return '内部调用参数配置有误';
                    }
                    if (!isset($data[$v['key_name']])) {
                        return $v['error_msg'];
                    }
                    if ($data_type == 'array') {
                        $length = count($data[$v['key_name']]);
                    } else {
                        $length = mb_strlen($data[$v['key_name']], 'utf-8');
                    }
                    $rule = explode(',', $v['checked_data']);
                    if (count($rule) == 1) {
                        if ($length > intval($rule[0])) {
                            return $v['error_msg'];
                        }
                    } else {
                        if ($length < intval($rule[0]) || $length > intval($rule[1])) {
                            return $v['error_msg'];
                        }
                    }
                    break;
                // 自定义函数
                case 'fun':
                    if (empty($v['checked_data']) || !function_exists($v['checked_data'])) {
                        return '验证函数为空或函数未定义';
                    }
                    $fun = $v['checked_data'];
                    if (!isset($data[$v['key_name']]) || !$fun($data[$v['key_name']])) {
                        return $v['error_msg'];
                    }
                    break;
                // 最小
                case 'min':
                    if (!isset($v['checked_data'])) {
                        return '验证最小值未定义';
                    }
                    if (!isset($data[$v['key_name']]) || $data[$v['key_name']] < $v['checked_data']) {
                        return $v['error_msg'];
                    }
                    break;
                // 最大
                case 'max':
                    if (!isset($v['checked_data'])) {
                        return '验证最大值未定义';
                    }
                    if (!isset($data[$v['key_name']]) || $data[$v['key_name']] > $v['checked_data']) {
                        return $v['error_msg'];
                    }
                    break;
                // 相等
                case 'eq':
                    if (!isset($v['checked_data'])) {
                        return '验证相等未定义';
                    }
                    if (!isset($data[$v['key_name']]) || $data[$v['key_name']] != $v['checked_data']) {
                        return $v['error_msg'];
                    }
                    break;
            }
        }
        return true;
    }
}