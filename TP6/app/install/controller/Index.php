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
// [ 安装程序 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\install\controller;

use think\facade\Db;
use think\Util as FileService;
class Index extends Common
{
    /**
     * 编码类型
     * @var array
     */
    protected $charset_type_list = [
        //
        'utf8mb4' => [
            //
            'charset' => 'utf8mb4',
            'collate' => 'utf8mb4_general_ci',
            'version' => 5.6,
        ],
        'utf8' => [
            //
            'charset' => 'utf8',
            'collate' => 'utf8_general_ci',
            'version' => 5.0,
        ],
    ];
    /**
     * 安装配置文件目录
     * @var string
     */
    protected $extra_data_path;
    /**
     * 静态文件目录
     * @var string
     */
    protected $static_path;
    /**
     * php最低版本要求
     * @var string
     */
    protected $need_php = '7.1.0';
    /**
     * 版本号
     * @var string
     */
    protected $cfg_soft_version;
    /**
     * 引导期间要加载的sql文件。
     *
     * @var string
     */
    protected $sqlFile = 'thinker.sql';
    /**
     * 引导期间要加载的数据库配置文件。
     *
     * @var string
     */
    protected $databaseFile = 'database.php';
    /**
     * 引导期间要加载的环境文件。
     *
     * @var string
     */
    protected $environmentFile = '.example.env';
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
        $this->environmentFileinit();
        $this->extra_data_path = $this->app_path . 'data' . DS;
        $this->static_path = $this->request->domain() . $this->root_dir . '/static/install';
        $this->cfg_soft_version = $this->params['version'];
        // 检测是否安装过程序
        if (is_file($this->extra_data_path . 'install.lock')) {
            //$this->setError('你已经安装过该系统，如果想重新安装，请先删除install应用data目录下的 install.lock 文件，然后再安装。');
        }
        if ($this->need_php > phpversion()) {
            $this->setError('本系统要求PHP版本 >= ' . $this->need_php . '，当前PHP版本为：' . phpversion() . '，请到虚拟主机控制面板里切换PHP版本，或联系空间商协助切换。');
        }
        if (!is_file($this->extra_data_path . $this->databaseFile)) {
            $this->setError("缺少必要的安装文件（{$this->databaseFile}）!");
        }
        if (!is_file($this->extra_data_path . $this->sqlFile)) {
            $this->setError("缺少必要的安装文件（{$this->sqlFile}）!");
        }
        if (!is_file($this->extra_data_path . $this->environmentFile)) {
            $this->setError("缺少必要的安装文件（{$this->environmentFile}）!");
        }
        $title = "Thinker安装向导";
        $powered = "Powered by Thinker";
        $steps = array(
            //
            '1' => '安装协议',
            '2' => '环境检测',
            '3' => '参数设置',
            '4' => '安装完成',
        );
        $domain = $this->request->domain() . $this->root_dir;
        $this->assign(compact('title', 'powered', 'steps', 'domain'));
    }
    /**
     * 获取密码加密字符
     * @access protected
     * @return string
     * @throws AuthException
     */
    protected function secureKey() : string
    {
        $default_key = 'a!takA:dlmcldEv,e';
        $secureKeyBase = config('auth.securekey', null);
        if (is_null($secureKeyBase)) {
            $secureKey = md5(substr(sha1($default_key), 0, 16));
        } else {
            $secureKey = base64_decode($secureKeyBase);
            $secureKey = md5(substr(sha1($secureKey), 0, 16));
        }
        return $secureKey;
    }
    /**
     * 使用协议
     * @access public
     * @return mixed
     */
    public function index()
    {
        return $this->view->filter(function ($content) {
            return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
        })->fetch(":step1");
    }
    /**
     * 环境检测
     * @access public
     * @return mixed
     */
    public function step2()
    {
        return $this->view->filter(function ($content) {
            return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
        })->fetch(":step2");
    }
    /**
     * 创建数据
     * @access public
     * @return mixed
     */
    public function step3()
    {
        $this->assign('charset_type_list', $this->charset_type_list);
        return $this->view->filter(function ($content) {
            return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
        })->fetch(":step3");
    }
    /**
     * 安装完成
     * @access public
     * @return mixed
     */
    public function step4()
    {
        // 检测是否是新安装
        if (session("INSTALLSTEP") == 'ISINSTALLED') {
            if (method_exists($this->request, 'getTime')) {
                $time = $this->request->getTime();
            } else {
                $time = $this->request->server('REQUEST_TIME');
            }
            $mt_rand_str = date("Ymdhis") . $this->secureKey();
            $constantFile = $this->root_path . 'app' . DS . 'admin' . DS . 'config' . DS . 'constant.php';
            $this->checkDirBuild(dirname($constantFile));
            $constantFileContent = <<<EOF
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
// [ 安装定义 ]
// --------------------------------------------------------------------------
declare (strict_types=1);

return [

EOF;
            $defineToScan = [
                //
                'install_date' => "'" . $time . "'" . "," . PHP_EOL,
                'serialnumber' => "'" . $mt_rand_str . "'" . "," . PHP_EOL,
            ];
            ksort($defineToScan);
            foreach ($defineToScan as $name => $val) {
                $constantFileContent .= '    ' . var_export($name, true) . ' => ' . $val;
            }
            $constantFileContent .= "];" . PHP_EOL;
            file_put_contents($constantFile, $constantFileContent);
            $html = $this->view->filter(function ($content) {
                return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
            })->fetch(":step4");
            touch($this->extra_data_path . 'install.lock');
            session('INSTALLSTEP', null);
            return $html;
        }
        return $this->redirect('install/index/index');
    }
    /**
     * 执行安装过程
     * @access public
     * @return array
     */
    public function performInstallation()
    {
        if ($this->request->isJson() || $this->request->isAjax()) {
            $this->setTimeout(1000);
            // 参数效验
            $params = input('param.');
            $ret = $this->paramsCheck($params);
            if ($ret['code'] != 0) {
                return $ret;
            }
            // 开始安装
            $db = $this->parseDbObj($params);
            // mysql版本
            $ret = $this->isVersion($db, $params['DB_CHARSET']);
            if ($ret['code'] != 0) {
                return $ret;
            }
            // 检查数据表是否存在
            if (!$this->isDbExist($db, $params['DB_NAME'])) {
                if ($this->dbNameCreate($db, $params['DB_NAME'], $params['DB_CHARSET'])) {
                    $db = $this->parseDbObj($params, $params['DB_NAME']);
                } else {
                    return $this->dataReturn('数据库创建失败', -1);
                }
            } else {
                $db = $this->parseDbObj($params, $params['DB_NAME']);
            }
            // 创建数据表
            $ret = $this->createTable($db, $params);
            if ($ret['code'] != 0) {
                return $ret;
            }
            // 生成配置文件
            return $this->createConfig($db, $params);
        }
        return $this->redirect('install/index/index');
    }
    /**
     * 创建数据表
     * @access protected
     * @param object $db db对象
     * @param array $params 数据库配置参数
     * @return array
     */
    protected function createTable(object $db, array $params = [])
    {
        // 读取数据文件
        $sqldata = file_get_contents($this->extra_data_path . $this->sqlFile);
        // 替换表前缀
        $sqldata = str_replace("`ey_", " `{$params['DB_PREFIX']}", $sqldata);
		$sqldata = preg_replace("/TYPE=(InnoDB|MyISAM|MEMORY)( DEFAULT CHARSET=[^; ]+)?/", "ENGINE=\\1 DEFAULT CHARSET=utf8", $sqldata);
        // 编码替换
        $charset = $this->charset_type_list[$params['DB_CHARSET']];
        if ($charset['charset'] != 'utf8') {
            $sqldata = str_replace('SET NAMES utf8;', "SET NAMES {$charset['charset']};", $sqldata);
            $sqldata = str_replace('CHARSET=utf8', "CHARSET={$charset['charset']}", $sqldata);
            $sqldata = str_replace('utf8_general_ci', "{$charset['collate']}", $sqldata);
        }
        // 数据表引擎替换
        if ($params['DB_ENGINE'] != 'MyISAM') {
            $sqldata = str_replace('ENGINE=MyISAM', "ENGINE={$params['DB_ENGINE']}", $sqldata);
        }
        // 转为数组
        $sql_all = preg_split("/;[\r\n]+/", $sqldata);
        $success = 0;
        $failure = 0;
        // 执行SQL语句
        /*foreach ($sql_all as $sql) {
              if (!empty($sql)) {
                  if ($db->execute($sql) !== false) {
                      $success++;
                  } else {
                      $failure++;
                  }
              }
          }*/
        for ($i = 0; $i < count($sql_all); $i++) {
            $sql = trim($sql_all[$i]);
            if (!empty($sql)) {
                if ($db->execute($sql) !== false) {
                    $success++;
                } else {
                    $failure++;
                }
            }
        }
        $max_i = 999999999;
        if ($max_i == $i) {
            return $this->dataReturn("数据库文件过大，执行条数超过{$max_i}条，请联系技术协助！", -1);
        }
        $result = ['success' => $success, 'failure' => $failure];
        if ($failure > 0) {
            return $this->dataReturn('sql运行失败[' . $failure . ']条', -1);
        }
        return $this->dataReturn('success', 0, $result);
    }
    /**
     * 数据库版本校验
     * @access protected
     * @param object $db db对象
     * @param string $db_charset 数据库编码
     * @return array
     */
    protected function isVersion(object $db, string $db_charset)
    {
        $data = $db->query("select version() AS version");
        if (empty($data[0]['version'])) {
            return $this->dataReturn('查询数据库版本失败', -1);
        } else {
            $mysql_version = str_replace('-log', '', $data[0]['version']);
            if ($mysql_version < $this->charset_type_list[$db_charset]['version']) {
                $msg = '数据库版本过低、需要>=' . $this->charset_type_list[$db_charset]['version'] . '、当前' . $mysql_version;
                return $this->dataReturn($msg, -1);
            }
        }
        return $this->dataReturn('success', 0);
    }
    /**
     * 数据库创建
     * @access protected
     * @param object $db db对象
     * @param string $db_name 数据库名称
     * @param string $db_charset 数据库编码
     * @return boolean
     */
    protected function dbNameCreate(object $db, string $db_name, string $db_charset)
    {
		$sql = "CREATE DATABASE IF NOT EXISTS {$db_name} DEFAULT CHARSET {$this->charset_type_list[$db_charset]['charset']} COLLATE {$this->charset_type_list[$db_charset]['collate']}";
        if ($db->execute($sql) !== false) {
            return $this->isDbExist($db, $db_name);
        }
        return false;
    }
    /**
     * 检查数据库是否存在
     * @access protected
     * @param object $db db对象
     * @param string $db_name 数据库名称
     * @return boolean
     */
    protected function isDbExist(object $db, string $db_name)
    {
        $temp = $db->query("show databases like '{$db_name}'");
        return !empty($temp);
    }
    /**
     * 解析参数并获取数据库操作对象
     * @access protected
     * @param array $params 配置参数
     * @param string $db_name 数据库名称
     * @return mixed
     */
    protected function parseDbObj(array $params = [], string $db_name = '') : object
    {
        $config = $this->parseConfig($params, $db_name);
        //Db::setConfig($config);
        config($config, 'database');
        try {
            $db = Db::connect($params['DB_TYPE'], true);
        } catch (\Throwable $e) {
            throw $e;
        }
        return $db;
    }
    /**
     * 解析数据库连接配置参数
     * @access protected
     * @param array $params 配置参数
     * @param string $db_name 数据库名称
     * @return array
     */
    protected function parseConfig(array $params = [], string $db_name = '') : array
    {
        $config = [
            // 默认数据连接标识
            'default' => 'mysql',
            // 数据库连接信息
            'connections' => ['mysql' => [
                // 数据库类型
                'type' => $params['DB_TYPE'],
                // 服务器地址
                'hostname' => $params['DB_HOST'],
                // 数据库名
                'database' => $db_name,
                // 用户名
                'username' => $params['DB_USER'],
                // 密码
                'password' => $params['DB_PWD'],
                // 端口
                'hostport' => $params['DB_PORT'],
                // 连接dsn
                'dsn' => $this->parseDsn($params, $db_name),
                // 数据库连接参数
                'params' => [
                    // TODOMORE
                    \PDO::ATTR_CASE => \PDO::CASE_LOWER,
                    \PDO::ATTR_EMULATE_PREPARES => true,
                ],
                // 数据库编码
                'charset' => $params['DB_CHARSET'],
                // 数据库表前缀
                'prefix' => $params['DB_PREFIX'],
                // 监听SQL
                'trigger_sql' => true,
            ]],
        ];
        return $config;
    }
    /**
     * 解析pdo连接的dsn信息
     * @access protected
     * @param  array $config 连接信息
     * @param string $db_name 数据库名称
     * @return string
     */
    protected function parseDsn(array $config, string $db_name = '') : string
    {
        if (!empty($config['DB_SOCKET'])) {
            $dsn = "{$config['DB_TYPE']}:unix_socket=" . $config['DB_SOCKET'];
        } elseif (!empty($config['DB_PORT'])) {
            $dsn = "{$config['DB_TYPE']}:host=" . $config['DB_HOST'] . ';port=' . $config['DB_PORT'];
        } else {
            $dsn = "{$config['DB_TYPE']}:host=" . $config['DB_HOST'];
        }
        if (!empty($db_name)) {
            $dsn .= ';dbname=' . $db_name;
        }
        if (!empty($config['DB_CHARSET'])) {
            $dsn .= ';charset=' . $config['DB_CHARSET'];
        }
        return $dsn;
    }
    /**
     * 参数校验
     * @access protected
     * @param array $data 原始数据
     * @return array
     */
    protected function paramsCheck(array $data = [])
    {
        // 校验数据
        $params = [
            // TODOMORE
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_TYPE',
                'error_msg' => '请选择数据库类型',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_ENGINE',
                'error_msg' => '请选择数据表引擎',
            ],
            [
                //
                'checked_type' => 'in',
                'key_name' => 'DB_CHARSET',
                'checked_data' => array_column($this->charset_type_list, 'charset'),
                'error_msg' => '请选择数据编码',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_HOST',
                'error_msg' => '请填写数据库服务器地址',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_PORT',
                'error_msg' => '请填写数据库端口',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_NAME',
                'error_msg' => '请填写数据库名',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_USER',
                'error_msg' => '请填写数据库用户名',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_PWD',
                'error_msg' => '请填写数据库密码',
            ],
            [
                //
                'checked_type' => 'empty',
                'key_name' => 'DB_PREFIX',
                'error_msg' => '请填写数据表前缀',
            ],
        ];
        $result = $this->paramsChecked($data, $params);
        if ($result !== true) {
            return $this->dataReturn($result, -1);
        }
        return $this->dataReturn('success', 0);
    }
    /**
     * 生成配置文件
     * @access protected
     * @param object $db db对象
     * @param array $params 配置参数
     * @return array
     */
    protected function createConfig(object $db, array $params = [])
    {
        // 清空缓存
        FileService::deleteDirectory($this->root_path . 'runtime', true);
        $params['ADMIN_USER_NAME'] = trim(addslashes($params['ADMIN_USER_NAME']));
        $params['ADMIN_USER_PWD'] = trim($params['ADMIN_USER_PWD']);
        if (method_exists($this->request, 'getTime')) {
            $time = $this->request->getTime();
        } else {
            $time = $this->request->server('REQUEST_TIME');
        }
        // 保存mysql的sql-mode模式参数
        $sql_mode = $db->query("SELECT @@global.sql_mode AS sql_mode");
        $system_sql_mode = isset($sql_mode[0]['sql_mode']) ? $sql_mode[0]['sql_mode'] : '';
        $result = $db->query(" SELECT value FROM `{$params['DB_PREFIX']}config` WHERE name = 'system_sql_mode' AND inc_type = 'system' LIMIT 1 ");
        if (!empty($result[0]['value'])) {
            $db->execute("UPDATE `{$params['DB_PREFIX']}config` SET `value` = '{$system_sql_mode}' WHERE name = 'system_sql_mode' AND inc_type = 'system'");
        } else {
            $db->execute(" INSERT INTO `{$params['DB_PREFIX']}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_sql_mode','{$system_sql_mode}','system','{$time}')");
        }
        // 读取数据库配置文件，并替换真实配置数据
        $strConfig1 = file_get_contents($this->extra_data_path . $this->databaseFile);
        $strConfig1 = str_replace('#SQL_MODE#', $system_sql_mode, $strConfig1);
        $strConfig1 = str_replace('#DB_TYPE#', $params['DB_TYPE'], $strConfig1);
        $strConfig1 = str_replace('#DB_HOST#', $params['DB_HOST'], $strConfig1);
        $strConfig1 = str_replace('#DB_NAME#', $params['DB_NAME'], $strConfig1);
        $strConfig1 = str_replace('#DB_USER#', $params['DB_USER'], $strConfig1);
        $strConfig1 = str_replace('#DB_PWD#', $params['DB_PWD'], $strConfig1);
        $strConfig1 = str_replace('#DB_PORT#', $params['DB_PORT'], $strConfig1);
        $strConfig1 = str_replace('#DB_PREFIX#', $params['DB_PREFIX'], $strConfig1);
        $strConfig1 = str_replace('#DB_CHARSET#', $params['DB_CHARSET'], $strConfig1);
        @chmod($this->root_path . 'config' . DS . 'database.php', 0755);
        $databaseresult = file_put_contents($this->root_path . 'config' . DS . 'database.php', $strConfig1);
        // 读取环境配置文件，并替换真实配置数据
        $strConfig2 = file_get_contents($this->extra_data_path . $this->environmentFile);
        $strConfig2 = str_replace('#SQL_MODE#', $system_sql_mode, $strConfig2);
        $strConfig2 = str_replace('#DB_TYPE#', $params['DB_TYPE'], $strConfig2);
        $strConfig2 = str_replace('#DB_HOST#', $params['DB_HOST'], $strConfig2);
        $strConfig2 = str_replace('#DB_NAME#', $params['DB_NAME'], $strConfig2);
        $strConfig2 = str_replace('#DB_USER#', $params['DB_USER'], $strConfig2);
        $strConfig2 = str_replace('#DB_PWD#', $params['DB_PWD'], $strConfig2);
        $strConfig2 = str_replace('#DB_PORT#', $params['DB_PORT'], $strConfig2);
        $strConfig2 = str_replace('#DB_PREFIX#', $params['DB_PREFIX'], $strConfig2);
        $strConfig2 = str_replace('#DB_CHARSET#', $params['DB_CHARSET'], $strConfig2);
        @chmod($this->root_path . '.env', 0755);
        $envresult = file_put_contents($this->root_path . '.env', $strConfig2);
        $web_cmspath = $this->root_dir;
        $web_basehost = $this->request->domain() . $this->root_dir;
        // 更新网站配置的网站网址
        $result = $db->query(" SELECT value FROM `{$params['DB_PREFIX']}config` WHERE name = 'web_basehost' AND inc_type = 'web' LIMIT 1 ");
        if (!empty($result[0]['value'])) {
            $db->execute("UPDATE `{$params['DB_PREFIX']}config` SET `value` = '{$web_basehost}' WHERE name = 'web_basehost' AND inc_type = 'web'");
        } else {
            $db->execute(" INSERT INTO `{$params['DB_PREFIX']}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('web_basehost','{$web_basehost}','web','{$time}')");
        }
        // 更新网站配置的CMS安装路径
        $result = $db->query(" SELECT value FROM `{$params['DB_PREFIX']}config` WHERE name = 'web_cmspath' AND inc_type = 'web' LIMIT 1 ");
        if (!empty($result[0]['value'])) {
            $db->execute("UPDATE `{$params['DB_PREFIX']}config` SET `value` = '{$web_cmspath}' WHERE name = 'web_cmspath' AND inc_type = 'web'");
        } else {
            $db->execute(" INSERT INTO `{$params['DB_PREFIX']}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('web_cmspath','{$web_cmspath}','web','{$time}')");
        }
        // 更新网站配置的CMS版本号
        $result = $db->query(" SELECT value FROM `{$params['DB_PREFIX']}config` WHERE name = 'system_version' AND inc_type = 'system' LIMIT 1 ");
        if (!empty($result[0]['value'])) {
            $db->execute("UPDATE `{$params['DB_PREFIX']}config` SET `value` = '{$this->cfg_soft_version}' WHERE name = 'system_version' AND inc_type = 'system'");
        } else {
            $db->execute(" INSERT INTO `{$params['DB_PREFIX']}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_version','{$this->cfg_soft_version}','system','{$time}')");
        }
        // 密码加密
        $auth_code = $this->secureKey();
        $result = $db->query(" SELECT value FROM `{$params['DB_PREFIX']}config` WHERE name = 'system_auth_code' AND inc_type = 'system' LIMIT 1 ");
        if (!empty($result[0]['value'])) {
            $auth_code = $result[0]['value'];
        } else {
            $db->execute(" INSERT INTO `{$params['DB_PREFIX']}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_auth_code','{$auth_code}','system','{$time}')");
        }
        $result = $db->query("SELECT admin_id FROM `{$params['DB_PREFIX']}admin`");
        if (!empty($result[0]['admin_id'])) {
            // 清空admin表
            $db->execute("truncate table `{$params['DB_PREFIX']}admin`");
            // 密码加密串，新安装程序，或者没有用户的程序，才给密码加密串
            $result = $db->query("SELECT users_id FROM `{$params['DB_PREFIX']}users`");
            if (empty($result[0]['users_id'])) {
                $db->execute("UPDATE `{$params['DB_PREFIX']}config` SET `value` = '{$auth_code}' WHERE name = 'system_auth_code' AND inc_type = 'system'");
            }
        } else {
            $db->execute("DELETE FROM `{$params['DB_PREFIX']}admin` WHERE user_name = '{$params['ADMIN_USER_NAME']}'");
        }
        // 插入管理员表thinker_admin
        if (method_exists($this->request, 'clientIP')) {
            $ip = $this->request->clientIP();
        } else {
            $ip = $this->request->ip();
        }
        $ip = empty($ip) ? "0.0.0.0" : $ip;
        $params['ADMIN_USER_PWD'] = md5($auth_code . $params['ADMIN_USER_PWD']);
        $db->execute(" INSERT INTO `{$params['DB_PREFIX']}admin` (`user_name`,`true_name`,`password`,`last_login`,`last_ip`,`login_cnt`,`status`,`add_time`) VALUES ('{$params['ADMIN_USER_NAME']}','{$params['ADMIN_USER_NAME']}','{$params['ADMIN_USER_PWD']}','0','{$ip}','1','1','{$time}')");
        if ($databaseresult === false || $envresult === false) {
            return $this->dataReturn('配置文件创建失败', -1);
        }
        session('INSTALLSTEP', 'ISINSTALLED');
        return $this->dataReturn('安装成功', 0);
    }
}