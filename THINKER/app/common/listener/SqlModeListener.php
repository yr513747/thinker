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
// [ 保存mysql的sql-mode模式参数 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\listener;

use think\Request;
use think\facade\Db;
use app\common\model\Config as ConfigModel;
class SqlModeListener
{
    /**
     * 事件执行入口
     * @access public
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        $this->setSqlMode($request);
    }
    /**
     * 保存mysql的sql-mode模式参数
     * @access protected
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    protected function setSqlMode(Request $request)
    {
        try {
            // 在后台模块才执行，以便提高性能
            if (in_array($request->app(), config('app.deny_multi_app_list', [])) && $request->isGet()) {
                $key = 'isset_saveSqlmode';
                $sessvalue = session($key);
                if (!empty($sessvalue)) {
                    return false;
                }
                session($key, 1);
                $sql_mode = Db::query("SELECT @@global.sql_mode AS sql_mode");
                $system_sql_mode = isset($sql_mode[0]['sql_mode']) ? $sql_mode[0]['sql_mode'] : '';
                ConfigModel::tpCache('system', ['system_sql_mode' => $system_sql_mode]);
                // 设置环境变量
                env(['sqlmode' => $system_sql_mode], 'database', true);
            }
        } catch (\PDOException $e) {
        } catch (\Exception $e) {
            throw $e;
        }
    }
}