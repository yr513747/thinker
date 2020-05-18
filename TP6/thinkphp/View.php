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
// [ 视图类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace think;

class View
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 请求对象
     * @var \think\Request
     */
    protected $request;
    /**
     * 模板引擎实例，驱动
     * @var object
     */
    protected $engine;
    /**
     * 视图驱动命名空间
     * @var string
     */
    protected $namespace = '\\think\\view\\driver\\';
    /**
     * 模板变量
     * @var array
     */
    protected $data = [];
    /**
     * 内容过滤
     * @var mixed
     */
    protected $filter;
    /**
     * 初始化
     * @access public
     * @param  App  $app  应用对象
     * @param  array $options 引擎参数
     * @return $this
     */
    public function init(App $app, array $options = []) : self
    {
        $this->app = $app;
        $this->request = $this->app->request;
        // 初始化模板引擎
        $this->engine($options);
        return $this;
    }
    public static function __make(App $app, Config $config) : self
    {
        $options = $config->get('template', []);
        return (new static())->init($app, $options);
    }
    /**
     * 设置当前模板解析的引擎
     * @access public
     * @param array $options 引擎参数
     * @return $this
     */
    public function engine(array $options = []) : self
    {
        if (empty($options)) {
            $options = $this->app->config->get('template', []);
        }
        if (empty($options['cache_path'])) {
            $options['cache_path'] = $this->app->getRuntimePath() . 'temp' . DIRECTORY_SEPARATOR;
        }
        if (empty($options['tpl_replace_string'])) {
            $options['tpl_replace_string'] = [];
        }
        // 基础替换字符串
        $base = $this->request->root();
        if ($this->app->config->get('route.cfg_multi_site')) {
            $root = $this->request->domain() . $this->request->rootUrl();
        } else {
            $root = $this->request->rootUrl();
        }
        $baseReplace = array(
            // 基础替换字符串
            '__ROOT_DIR__' => isset($GLOBALS['_M']['root_dir']) ? $GLOBALS['_M']['root_dir'] : $this->request->rootUrl(),
            '__HOST__' => $this->request->host(),
            '__DOMAIN__' => $this->request->domain(),
            '__URL__' => $base . '/' . $this->request->app() . '/' . self::parseName($this->request->controller()),
            '__PUBLIC__' => $root,
            '__STATIC__' => $root . '/static',
            '__SKIN__' => $root . '/static/' . $this->request->app(),
            '__ADMIN_SKIN__' => $root . '/static/admin',
            '__WEAPP_SKIN__' => $root . '/weapp',
        );
        $options['tpl_replace_string'] = array_merge($baseReplace, (array) $options['tpl_replace_string']);
        $type = !empty($options['type']) ? $options['type'] : 'Think';
        $class = false !== strpos($type, '\\') ? $type : $type;
        if (isset($options['type'])) {
            unset($options['type']);
        }
        $this->engine = Container::factory($class, $this->namespace, $options);
        return $this;
    }
    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template) : bool
    {
        return $this->engine->exists($template);
    }
    /**
     * 模板变量赋值
     * @access public
     * @param mixed $name  变量名
     * @param mixed $value 变量值
     * @return $this
     */
    public function assign($name, $value = '') : self
    {
        if (is_array($name)) {
            $this->data = array_merge($this->data, $name);
        } else {
            $this->data[$name] = $value;
        }
        return $this;
    }
    /**
     * 视图过滤
     * @access public
     * @param Callable $filter 过滤方法或闭包
     * @return $this
     */
    public function filter(callable $filter = null) : self
    {
        $this->filter = $filter;
        return $this;
    }
    /**
     * 解析和获取模板内容 用于输出
     * @access public
     * @param string $template 模板文件名或者内容
     * @param array  $vars     模板变量
     * @return string
     * @throws \Exception
     */
    public function fetch(string $template = '', array $vars = []) : string
    {
        return $this->getContent(function () use($vars, $template) {
            $this->engine->fetch($template, array_merge($this->data, $vars));
        });
    }
    /**
     * 渲染内容输出
     * @access public
     * @param string $content 内容
     * @param array  $vars    模板变量
     * @return string
     */
    public function display(string $content, array $vars = []) : string
    {
        return $this->getContent(function () use($vars, $content) {
            $this->engine->display($content, array_merge($this->data, $vars));
        });
    }
    /**
     * 获取模板引擎渲染内容
     * @param $callback
     * @return string
     * @throws \Exception
     */
    protected function getContent($callback) : string
    {
        // 页面缓存
        ob_start();
        ob_implicit_flush(0);
        // 渲染输出
        try {
            $callback();
        } catch (\Exception $e) {
            ob_end_clean();
            throw $e;
        }
        // 获取并清空缓存
        $content = ob_get_clean();
        // 内容过滤标签
        $this->app->event->trigger('ViewFilter', $content);
        if ($this->filter) {
            $content = call_user_func_array($this->filter, [$content]);
        }
        return $content;
    }
    /**
     * 模板变量赋值
     * @access public
     * @param string $name  变量名
     * @param mixed  $value 变量值
     * @return void
     */
    public function __set($name, $value) : void
    {
        $this->data[$name] = $value;
    }
    /**
     * 取得模板显示变量的值
     * @access protected
     * @param string $name 模板变量
     * @return mixed
     */
    public function __get($name)
    {
        return $this->data[$name];
    }
    /**
     * 检测模板变量是否设置
     * @access public
     * @param string $name 模板变量名
     * @return bool
     */
    public function __isset($name) : bool
    {
        return isset($this->data[$name]);
    }
    /**
     * 字符串命名风格转换
     * type 0 将 Java 风格转换为 C 的风格 1 将 C 风格转换为 Java 的风格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}