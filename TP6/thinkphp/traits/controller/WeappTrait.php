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

use think\facade\Db;
trait WeappTrait
{
    /**
     * 验证插件的配置完整性
     * @access protected
     * @return void
     * @throws \Exception
     */
    public final function checkConfig() : void
    {
        $config_check_keys = array('code', 'name', 'description', 'scene', 'author', 'version', 'min_version');
        $config = (include $this->config_file);
        foreach ($config_check_keys as $value) {
            if (!array_key_exists($value, $config)) {
                throw new \Exception(sprintf('The plug-in configuration file config.php does not conform to the official specification, and the %s array element is missing', $value));
            }
        }
    }
    /**
     * 获取插件信息
     * @access protected
     * @param  string $code  插件标识
     * @return mixed
     */
    protected final function getWeappInfo(string $code = '')
    {
        static $_weapp = array();
        if (empty($code)) {
            $config = $this->getConfig();
            $code = !empty($config['code']) ? $config['code'] : $this->weapp_app_name;
        }
        if (isset($_weapp[$code])) {
            return $_weapp[$code];
        }
        $values = array();
        try {
            $config = Db::name('weapp')->where('code', $code)->getField('config');
        } catch (\Throwable $e) {
            $config = array();
        }
        if (!empty($config)) {
            $values = json_decode($config, true);
        }
        $_weapp[$code] = $values;
        return $values;
    }
    /**
     * 获取插件的配置
     * @access protected
     * @return mixed
     */
    protected final function getConfig()
    {
        static $config = array();
        if (empty($config)) {
            $config = (include $this->config_file);
        }
        return $config;
    }
    /**
     * 插件使用说明
     * @access public
     * @return mixed
     */
    public function doc()
    {
        return $this->success("该插件开发者未完善使用指南！", null, '', 3);
    }
}