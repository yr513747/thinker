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

use think\App;
use think\Request;
use think\facade\Db;
use app\common\model\Config as ConfigModel;
class SqlModeListener
{
    /**
     * 当前应用实例
     * @var \think\App
     */
    protected $app;
    protected $envFile = '.env';
    protected $databaseFile = 'database.php';
    /**
     * 构造方法
     * @access public
     * @param  App  $app  应用对象
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->envFile = $this->app->getRootPath() . $this->envFile;
        $this->databaseFile = $this->app->getConfigPath() . $this->databaseFile;
    }
    /**
     * 事件执行入口
     * @access public
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {
        $this->saveSqlModeParametersOfMysql($request);
    }
    /**
     * 保存mysql的sql-mode模式参数
     * @access protected
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    protected function saveSqlModeParametersOfMysql(Request $request)
    {
        // 在后台模块才执行，以便提高性能
        if (in_array($this->app->http->getName(), $this->app->config->get('app.deny_multi_app_list', ['admin'])) && $request->isGet()) {
            try {
                if (empty($GLOBALS['__isset_save_sqlmode'])) {
                    $sql_mode = Db::query("SELECT @@global.sql_mode AS sql_mode");
                    $system_sql_mode = isset($sql_mode[0]['sql_mode']) ? $sql_mode[0]['sql_mode'] : '';
                    ConfigModel::tpCache('system', ['system_sql_mode' => $system_sql_mode]);
                    // 设置环境变量
                    $this->settingEnvironmentVariables($this->envFile, $system_sql_mode);
                    // 设置数据库配置
                    $this->setDatabaseConfiguration($this->databaseFile, $system_sql_mode);
                    $GLOBALS['__isset_save_sqlmode'] = true;
                }
            } catch (\PDOException $e) {
                return false;
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    /**
     * 设置环境变量
     * @access protected
     * @param string $envFile   环境变量文件
     * @param string $system_sql_mode   mysql的sql-mode模式参数
     * @return void
     */
    protected function settingEnvironmentVariables(string $envFile, string $system_sql_mode = null)
    {
        $envcontentarr = [];
        if (is_file($envFile)) {
            $envcontentarr = parse_ini_file($envFile, true) ?: [];
            $array_keys = implode("|", array_keys($envcontentarr));
            if (strpos(strtoupper($array_keys), 'DATABASE_SQLMODE') !== false) {
                // 解析内容
                foreach ($envcontentarr as $name => $val) {
                    if (strtoupper($name) == 'DATABASE_SQLMODE') {
                        $val = $system_sql_mode;
                    }
                    // 装回去
                    $envcontentarr[$name] = $val;
                }
            } else {
                // 解析内容
                foreach ($envcontentarr as $name => $val) {
                    if (strtoupper($name) == 'DATABASE') {
                        foreach ($val as $name2 => $val2) {
                            if (strtoupper($name2) == 'SQLMODE') {
                                $val2 = $system_sql_mode;
                            }
                            // 装回去
                            $val[$name2] = $val2;
                        }
                    }
                    // 装回去
                    $envcontentarr[$name] = $val;
                }
            }
        }
        $string = PHP_EOL;
        if (!empty($envcontentarr)) {
            $envcontentarr = array_change_key_case($envcontentarr, CASE_UPPER);
            foreach ($envcontentarr as $key => $val) {
                if (is_array($val)) {
                    foreach ($val as $k => $v) {
                        $k = strtoupper($k);
                        $string .= "{$key}_{$k} = \"{$v}\"" . PHP_EOL;
                        if ($v == true) {
                            $string = str_replace('"1"', "true", $string);
                        }
                        if ($v == false) {
                            $string = str_replace('"0"', "false", $string);
                        }
                    }
                } else {
                    $string .= "{$key} = \"{$val}\"" . PHP_EOL;
                    if ($val == true) {
                        $string = str_replace('"1"', "true", $string);
                    }
                    if ($val == false) {
                        $string = str_replace('"0"', "false", $string);
                    }
                }
            }
            file_put_contents($envFile, $string);
        }
    }
    /**
     * 设置数据库配置
     * @access protected
     * @param string $$databaseFile   数据库配置文件
     * @param string $system_sql_mode   mysql的sql-mode模式参数
     * @return void
     */
    protected function setDatabaseConfiguration(string $databaseFile, string $system_sql_mode = null)
    {
        if (is_file($databaseFile)) {
            //分析php源码
            $arr = file($databaseFile);
            for ($i = 0, $j = count($arr); $i < $j; $i++) {
                if (is_string($arr[$i]) && strpos($arr[$i], 'system_sql_mode') !== false) {
                    $arr[$i] = "    'system_sql_mode' => env('database.sqlmode', '" . $system_sql_mode . "')," . PHP_EOL;
                    break;
                }
            }
            $content = implode("", $arr);
            if (strpos($content, 'system_sql_mode') !== false) {
                @file_put_contents($databaseFile, $content);
            }
        }
    }
}