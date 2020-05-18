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
declare (strict_types=1);
namespace Thinker\traits\app;

use think\Validate;
use think\helper\Str;
use think\exception\ClassNotFoundException;
/**
 * 插件操作基本函数，基于think本身的方法结合程序自身的需求做一些简单的修改
 */
trait WeAppTrait
{

    /**
     * 实例化应用类库
     * @access public
     * @param  string $name         类名称
     * @param  string $layer        业务层名称
     * @param  bool   $weappendSuffix 是否添加类名后缀
     * @param  string $common       公共应用名
     * @return object
     * @throws ClassNotFoundException
     */
    public function weappCreate(string $name = null, string $layer = null, bool $weappendSuffix = false, string $common = 'common')
    {
        $guid = $name . $layer;
        if ($this->bound($guid)) {
            return $this->make($guid, [], true);
        }
        list($weapp, $class) = $this->parseWeAppAndClass($name, $layer, $weappendSuffix);
        if (class_exists($class)) {
            $object = $this->make($class, [], true);
        } else {
            $class = str_replace('\\' . $weapp . '\\', '\\' . $common . '\\', $class);
            if (class_exists($class)) {
                $object = $this->make($class, [], true);
            } else {
                throw new ClassNotFoundException('class not exists:' . $class, $class);
            }
        }
        $this->bind($guid, $class);
        return $object;
    }
    /**
     * 解析应用和类名
     * @access protected
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $weappendSuffix 是否添加类名后缀
     * @return array
     */
    public function parseWeAppAndClass(string $name = null, string $layer = null, bool $weappendSuffix = false) : array
    {
        if (false !== strpos($name, "\\")) {
            $class = $name;
            $weapp = $this->request->param($this->config('route.weapp_app', 'm'));
        } else {
            if (false !== strpos($name, "/")) {
                list($weapp, $name) = explode("/", $name, 2);
            } else {
                $weapp = $this->request->param($this->config('route.weapp_app', 'm'));
            }
            $class = $this->weappParseClass($weapp, $layer, $name, $weappendSuffix);
        }
        return [$weapp, $class];
    }
    /**
     * 解析应用类的类名
     * @access public
     * @param  string $weapp 应用名
     * @param  string $layer  层名 weappController model ...
     * @param  string $name   类名
     * @param  bool   $weappendSuffix
     * @return string
     */
    public function weappParseClass(string $weapp = null, string $layer = null, string $name = null, bool $weappendSuffix = false) : string
    {
        $name = str_replace(['/', '.'], '\\', $name);
        $array = explode('\\', $name);
        $class = $this->parseName(array_pop($array), 1) . ($this->config('app.class_suffix') || $weappendSuffix ? ucfirst($layer) : '');
        $path = $array ? implode('\\', $array) . '\\' : '';
        return $this->weappnamespace . '\\' . ($weapp ? $weapp . '\\' : '') . $layer . '\\' . $path . $class;
    }
    /**
     * 实例化（分层）控制器 格式：[应用名/]控制器名
     * @access public
     * @param  string $name              资源地址
     * @param  string $layer             控制层名称
     * @param  bool   $weappendSuffix      是否添加类名后缀
     * @param  string $empty             空控制器名称
     * @return object
     * @throws ClassNotFoundException
     */
    public function weappController(string $name = null, string $layer = 'controller', bool $weappendSuffix = false, string $empty = null)
    {
        list($weapp, $class) = $this->parseWeAppAndClass($name, $layer, $weappendSuffix);
        if (class_exists($class)) {
            return $this->make($class, [], true);
        } elseif ($empty && class_exists($emptyClass = $this->weappParseClass($weapp, $layer, $empty, $weappendSuffix))) {
            return $this->make($emptyClass, [], true);
        }
        throw new ClassNotFoundException('class not exists:' . $class, $class);
    }
    /**
     * 实例化验证类 格式：[应用名/]验证器名
     * @access public
     * @param  string $name         资源地址
     * @param  string $layer        验证层名称
     * @param  bool   $weappendSuffix 是否添加类名后缀
     * @param  string $common       公共应用名
     * @return Validate
     * @throws ClassNotFoundException
     */
    public function weappValidate(string $name = null, string $layer = 'validate', bool $weappendSuffix = false, string $common = 'common') : Validate
    {
        $name = $name ?: $this->config('app.default_validate');
        if (empty($name)) {
            return new Validate();
        }
        return $this->weappCreate($name, $layer, $weappendSuffix, $common);
    }
    /**
     * 远程调用应用的操作方法 参数格式 [应用/控制器/]操作
     * @access public
     * @param  string       $url          调用地址
     * @param  string|array $vars         调用参数 支持字符串和数组
     * @param  string       $layer        要调用的控制层名称
     * @param  bool         $weappendSuffix 是否添加类名后缀
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function weappAction(string $url = null, $vars = '', string $layer = 'controller', bool $weappendSuffix = false)
    {
        // 自动获取请求变量
        if (empty($vars)) {
            $vars = $this->config('route.url_param_type') ? $this->request->route() : $this->request->param();
        }
        $info = pathinfo($url);
        $action = $info['basename'];
        $weapp = '.' != $info['dirname'] ? $info['dirname'] : $this->request->param($this->config('route.weapp_controller', 'c'));
        $class = $this->weappController($weapp, $layer, $weappendSuffix);
        if (is_scalar($vars)) {
            if (strpos((string)$vars, '=')) {
                parse_str($vars, $vars);
            } else {
                $vars = [$vars];
            }
        }
		$this->request->withParam($vars);
        return $this->invokeMethod([$class, $action . $this->config('route.action_suffix')], $vars);
    }
}