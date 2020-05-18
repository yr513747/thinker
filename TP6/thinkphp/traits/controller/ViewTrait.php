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
namespace think\traits\controller;

trait ViewTrait
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 请求对象
     * @var \think\Request
     */
    protected $request;
    /**
     * @var \think\View 视图类实例
     */
    protected $view;
    /**
     * 静态模板缓存实例
     * @var \think\HtmlCache
     */
    protected $HtmlCache;
    /**
     * 写入静态模板缓存用于页面展示(运营模式)
     * @access protected
     * @param  mixed $html 缓存值
     * @return bool
     */
    protected function writeHtmlCache($html = '') : bool
    {
        if (!in_array($this->params['app_name'], config('app.deny_multi_app_list', []))) {
            $html_cache_type = config('htmlcache.cache_type', []);
            $param = input('param.');
            $m_c_a_str = $this->params['app_name'] . '_' . $this->params['controller_name'] . '_' . $this->params['action_name'];
            // 应用_控制器_方法
            $m_c_a_str = strtolower($m_c_a_str);
            foreach ($html_cache_type as $key => $val) {
                $key = strtolower($key);
                if ($key != $m_c_a_str) {
                    //不是当前 应用 控制器 方法 直接跳过
                    continue;
                }
                if (empty($val['filename'])) {
                    continue;
                }
                $filename = '';
                // 组合参数
                if (isset($val['p'])) {
                    foreach ($val['p'] as $k => $v) {
                        if (isset($param[$v])) {
                            if (preg_match('/\\/$/i', $filename)) {
                                $filename .= $param[$v];
                            } else {
                                if (!empty($filename)) {
                                    $filename .= '_';
                                }
                                $filename .= $param[$v];
                            }
                        }
                    }
                }
                empty($filename) && ($filename = 'index');
                $path = $val['filename'];
                if ($this->request->isMobile()) {
                    $path .= "_mobile";
                } else {
                    $path .= "_pc";
                }
                $filename = $path . "_html{$filename}.html";
                $expire = !empty($val['cache']) ? $val['cache'] : null;
                return $this->HtmlCache->set($filename, $html, $expire);
            }
        }
        return false;
    }
    /**
     * 读取静态模板缓存用于页面展示(运营模式)
     * @access protected
     * @return mixed
     */
    protected function readHtmlCache()
    {
        if (!in_array($this->params['app_name'], config('app.deny_multi_app_list', []))) {
            $html_cache_type = config('htmlcache.cache_type', []);
            $param = input('param.');
            $m_c_a_str = $this->params['app_name'] . '_' . $this->params['controller_name'] . '_' . $this->params['action_name'];
            // 应用_控制器_方法
            $m_c_a_str = strtolower($m_c_a_str);
            foreach ($html_cache_type as $key => $val) {
                $key = strtolower($key);
                if ($key != $m_c_a_str) {
                    //不是当前 应用 控制器 方法 直接跳过
                    continue;
                }
                if (empty($val['filename'])) {
                    continue;
                }
                $filename = '';
                // 组合参数
                if (isset($val['p'])) {
                    foreach ($val['p'] as $k => $v) {
                        if (isset($param[$v])) {
                            if (preg_match('/\\/$/i', $filename)) {
                                $filename .= $param[$v];
                            } else {
                                if (!empty($filename)) {
                                    $filename .= '_';
                                }
                                $filename .= $param[$v];
                            }
                        }
                    }
                }
                empty($filename) && ($filename = 'index');
                $path = $val['filename'];
                if ($this->request->isMobile()) {
                    $path .= "_mobile";
                } else {
                    $path .= "_pc";
                }
                $filename = $path . "_html{$filename}.html";
                if ($this->HtmlCache->has($filename)) {
                    return $this->HtmlCache->send($filename, true);
                }
            }
        }
        return false;
    }
    /**
     * 删除静态模板缓存(调试模式)
     * @access protected
     * @return bool
     */
    protected function deleteHtmlCache() : bool
    {
        return $this->HtmlCache->clear();
    }
    /**
     * 检测是否存在模板文件
     * @access protected
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    protected function exists($template) : bool
    {
        return $this->view->exists($template);
    }
    /**
     * 视图过滤
     * @access protected
     * @param  Callable $filter 过滤方法或闭包
     * @return $this
     */
    protected function filter(callable $filter = null) : self
    {
        $this->view->filter($filter);
		return $this;
    }
    /**
     * 加载模板输出
     * @access protected
     * @param  string $template
     * @param  array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     * @throws \think\Exception
     */
    protected function fetch($template = '', $vars = [])
    {
        $html = $this->view->fetch($template, $vars);
        //尝试写入静态缓存
        if (false === $this->app->isDebug() && true === $this->request->isGet()) {
            $this->writeHtmlCache($html);
        }
        return $html;
    }
    /**
     * 渲染内容输出
     * @access protected
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @return mixed
     */
    protected function display($content = '', $vars = [])
    {
        $html = $this->view->display($content, $vars);
        //尝试写入静态缓存
        if (false === $this->app->isDebug() && true === $this->request->isGet()) {
            $this->writeHtmlCache($html);
        }
        return $html;
    }
    /**
     * 模板变量赋值
     * @access protected
     * @param  string|array $name  模板变量
     * @param  mixed        $value 变量值
     * @return void
     */
    protected function assign($name, $value = null) : void
    {
        $this->view->assign($name, $value);
    }
    /**
     * 初始化模板引擎
     * @access protected
     * @param  array $engine 引擎参数
     * @return void
     */
    protected function engine($engine) : void
    {
        $this->view->engine($engine);
    }
}