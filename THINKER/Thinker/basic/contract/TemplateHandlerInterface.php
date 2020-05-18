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
// [ 视图驱动接口 ]
// --------------------------------------------------------------------------
declare (strict_types=1);

namespace Thinker\basic\contract;

interface TemplateHandlerInterface extends CommonFuncInterface
{
    /**
     * 检测是否存在模板文件
     * @access public
     * @param  string $template 模板文件或者模板规则
     * @return bool
     */
    public function exists($template);
	
	/**
     * 视图过滤
     * @access public
     * @param Callable $filter 过滤方法或闭包
     * @return $this
     */
    public function filter(callable $filter = null);
	
    /**
     * 加载模板输出
     * @access public
     * @param string $template
     * @param array $vars           模板文件名
     * @return false|mixed|string   模板输出变量
     * @throws \think\Exception
     */
    public function fetch($template = '', $vars = []);
	
    /**
     * 渲染内容输出
     * @access public
     * @param  string $content 模板内容
     * @param  array  $vars    模板输出变量
     * @return mixed
     */
    public function display($content = '', $vars = []);
	
    /**
     * 模板变量赋值
     * @access public
     * @param string|array $name  模板变量
     * @param mixed        $value 变量值
     * @return $this
     */
    public function assign($name, $value = null);
	
    /**
     * 初始化模板引擎
     * @access public
     * @param  array $engine 引擎参数
     * @return $this
     */
    public function engine($engine);
	
    /**
     * 写入静态模板缓存用于页面展示(运营模式)
     * @access public
     * @param mixed $html 缓存值
     * @return bool
     */
    public function writeHtmlCache($html = '');
	
    /**
     * 读取静态模板缓存用于页面展示(运营模式)
     * @access public
     * @return mixed
     */
    public function readHtmlCache();
}