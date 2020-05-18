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
// [ 设置动态数据 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\listener;

use think\App;
use app\common\model\Config as ConfigModel;
class ConfigSetupListener
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }
    /**
     * 事件执行入口
     * @access public
     * @return void
     */
    public function handle()
    {
        $this->setConfiguration();
    }
    /**
     * 动态设置配置参数
     * @access protected
     * @return void
     * @throws \PDOException|\Exception
     */
    protected function setConfiguration()
    {
        try {
            $this->app->config->set(ConfigModel::tpCache('global'), 'tpcache');
            // 模板错误提示
			$web_exception = $this->app->config->get('tpcache.web_exception');
            if (!empty($web_exception)) {
                $this->app->config->set(['web_exception' => $web_exception], 'app');
            }
        } catch (\PDOException $e) {
			return false;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}