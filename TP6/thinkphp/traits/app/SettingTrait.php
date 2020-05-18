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
namespace think\traits\app;

trait SettingTrait
{
    /**
     * 开启应用调试模式
     * @access public
     * @param bool $debug 开启应用调试模式
     * @return $this
     */
    public function debug(bool $debug = true)
    {
        $this->appDebug = $debug;
        return $this;
    }
    /**
     * 是否为调试模式
     * @access public
     * @return bool
     */
    public function isDebug() : bool
    {
        return $this->appDebug;
    }
    /**
     * 设置应用命名空间
     * @access public
     * @param string $namespace 应用命名空间
     * @return $this
     */
    public function setNamespace(string $namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }
    /**
     * 获取应用类库命名空间
     * @access public
     * @return string
     */
    public function getNamespace() : string
    {
        return $this->namespace;
    }
    /**
     * 获取框架版本
     * @access public
     * @return string
     */
    public function version() : string
    {
        return static::VERSION;
    }
    /**
     * 获取应用根目录
     * @access public
     * @return string
     */
    public function getRootPath() : string
    {
        return $this->rootPath;
    }
    /**
     * 获取应用基础目录
     * @access public
     * @return string
     */
    public function getBasePath() : string
    {
        return $this->rootPath . 'app' . DIRECTORY_SEPARATOR;
    }
    /**
     * 获取当前应用目录
     * @access public
     * @return string
     */
    public function getAppPath() : string
    {
        return $this->appPath;
    }
    /**
     * 设置应用目录
     * @param string $path 应用目录
     */
    public function setAppPath(string $path)
    {
        if (substr($path, -1) != DS) {
            $path .= DS;
        }
        $this->appPath = $path;
    }
    /**
     * 获取应用运行时目录
     * @access public
     * @return string
     */
    public function getRuntimePath() : string
    {
        return $this->runtimePath;
    }
    /**
     * 设置runtime目录
     * @param string $path 定义目录
     */
    public function setRuntimePath(string $path) : void
    {
        $this->runtimePath = $path;
    }
    /**
     * 获取核心框架目录
     * @access public
     * @return string
     */
    public function getThinkPath() : string
    {
        return $this->thinkPath;
    }
    /**
     * 获取应用配置目录
     * @access public
     * @return string
     */
    public function getConfigPath() : string
    {
        return $this->rootPath . 'config' . DIRECTORY_SEPARATOR;
    }
    /**
     * 获取配置后缀
     * @access public
     * @return string
     */
    public function getConfigExt() : string
    {
        return $this->configExt;
    }
    /**
     * 获取应用开启时间
     * @access public
     * @return float
     */
    public function getBeginTime() : float
    {
        return $this->beginTime;
    }
    /**
     * 获取应用初始内存占用
     * @access public
     * @return integer
     */
    public function getBeginMem() : int
    {
        return $this->beginMem;
    }
    /**
     * 获取公共/web目录的路径。
     *
     * @return string
     */
    public function publicPath()
    {
        return $this->rootPath . 'public' . DS;
    }
    /**
     * 获取环境文件的完全限定路径。
     *
     * @return string
     */
    public function environmentFilePath()
    {
        return $this->rootPath . $this->environmentFile;
    }
    /**
     * 获取路由目录
     * @access protected
     * @return string
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }
    /**
     * 设置路由目录
     * @access public
     * @param string $path 路由定义目录
     * @return string
     */
    public function setRoutePath(string $path) : void
    {
        $this->routePath = $path;
    }
    /**
     * 是否运行在命令行下
     * @return bool
     */
    public function runningInConsole()
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }
    /**
     * 是否运行在win下
     * @return bool
     */
    public function runningInWin()
    {
        return strpos(PHP_OS, 'WIN') !== false || PHP_OS_FAMILY === 'Windows';
    }
	/**
     * 是否为64位操作系统
     * @access public
     * @return bool
     */
    public function runningInX64() : bool
    {
        return PHP_INT_SIZE === 8;
    }
    /**
     * 记录调试信息
     * @access public
     * @param  mixed  $msg  调试信息
     * @param  string $type 信息类型
     * @return void
     */
    public function log($msg, $type = 'info')
    {
        $this->isDebug() && $this->log->record($msg, $type);
    }
    /**
     * 获取配置参数 为空则获取所有配置
     * @access public
     * @param  string $name    配置参数名（支持多级配置 .号分割）
     * @param  mixed  $default 默认值
     * @return mixed
     */
    public function config($name = null, $default = null)
    {
        return $this->config->get($name, $default);
    }
    /**
     * 获取应用根目录
     * @access protected
     * @return string
     */
    protected function getDefaultRootPath() : string
    {
        $path = dirname(dirname(dirname(dirname($this->thinkPath))));
        return $path . DIRECTORY_SEPARATOR;
    }
    /**
     * core目录
     * @access public
     * @return string
     */
    public function getcorePath()
    {
        return $this->getRootPath() . 'core' . DS;
    }
    /**
     * 获取vendor目录
     * @access public
     * @return string
     */
    public function getVendorPath()
    {
        return $this->getRootPath() . 'vendor' . DS;
    }
    /**
     * 获取应用基础目录
     * @access public
     * @return string
     */
    public function getAppBasePath()
    {
        return $this->getRootPath() . 'app' . DS;
    }
    /**
     * extend目录
     * @access public
     * @return string
     */
    public function getExtendPath()
    {
        return $this->getRootPath() . 'extend' . DS;
    }
    /**
     * include目录
     * @access public
     * @return string
     */
    public function getIncPath()
    {
        return $this->getRootPath() . 'include' . DS;
    }
    /**
     * 助手文件目录
     * @access public
     * @return string
     */
    public function getHelpersPath()
    {
        return $this->getIncPath() . 'helpers' . DS;
    }
    /**
     * Composer安装路径
     * @access public
     * @return string
     */
    public function getComposerPath()
    {
        return $this->getVendorPath() . 'composer' . DS;
    }
}