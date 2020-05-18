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
// [ 程序安装控制文件 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\install\controller;

use Thinker\basic\BaseController;
use Thinker\traits\app\ErrorPage;
use think\exception\HttpResponseException;
use think\Response;
use app\install\pdo\facade\Db;
use PDOException;
use Thinker\exceptions\AuthException;
class Index extends BaseController
{
    use ErrorPage;
    /**
     * 程序根目录
     * @var string
     */
    protected $root_path;
    /**
     * 当前应用目录
     * @var string
     */
    protected $app_path;
    /**
     * 安装配置文件目录
     * @var string
     */
    protected $extra_path;
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
     * 密码加密串配置文件。
     *
     * @var string
     */
    protected $secureKeyFile = 'auth.json';
    /**
     * 初始化设置
     * @access  protected
     * @return  void
     * @throws HttpResponseException
     */
    protected function initialize()
    {
        parent::initialize();
		//$this->loadForm();
		$this->app->environmentFileinit();
        $this->root_path = $this->app->getRootPath();
        $this->app_path = $this->app->getAppPath();
        $this->extra_path = $this->app_path . 'extra' . DS;
        $this->static_path = $this->request->domain() . $this->root_dir . '/install';
        $this->cfg_soft_version = $this->params['version'];
        // 检测是否安装过程序
        if (is_file($this->extra_path . 'install.lock')) {
            $this->setError('你已经安装过该系统，如果想重新安装，请先删除install应用extra目录下的 install.lock 文件，然后再安装。');
        }
        $this->setTimeout(1000);
        if (phpversion() <= $this->need_php) {
            @set_magic_quotes_runtime(0);
        }
        if ($this->need_php > phpversion()) {
            $this->setError('本系统要求PHP版本 >= ' . $this->need_php . '，当前PHP版本为：' . phpversion() . '，请到虚拟主机控制面板里切换PHP版本，或联系空间商协助切换。');
        }
        if (!is_file($this->extra_path . $this->secureKeyFile)) {
            $this->setError("缺少必要的安装文件（{$this->secureKeyFile}）!");
        }
        if (!is_file($this->extra_path . $this->databaseFile)) {
            $this->setError("缺少必要的安装文件（{$this->databaseFile}）!");
        }
        if (!is_file($this->extra_path . $this->sqlFile)) {
            $this->setError("缺少必要的安装文件（{$this->sqlFile}）!");
        }
        if (!is_file($this->extra_path . $this->environmentFile)) {
            $this->setError("缺少必要的安装文件（{$this->environmentFile}）!");
        }
        $title = "Thinker安装向导";
        $powered = "Powered by Thinker";
        $steps = array(
            //
            '1' => '安装许可协议',
            '2' => '运行环境检测',
            '3' => '安装参数设置',
            '4' => '安装详细过程',
            '5' => '安装完成',
        );
        $domain = $this->request->domain() . $this->root_dir;
        $this->assign(compact('title', 'powered', 'steps', 'domain'));
    }
    /**
     * 设置脚本运行超时时间
     * 0表示不限制，支持连贯操作
     * @param  integer  $time
     * @return  $this
     */
    protected function setTimeout($time = null)
    {
        if (!is_null($time)) {
            @set_time_limit($time) || ini_set("max_execution_time", $time);
        }
        return $this;
    }
    /**
     * 设置并抛出错误信息
     * @param  string|array $message 错误信息
     * @throws HttpResponseException
     */
    protected function setError($message)
    {
        $options = array();
        $options['error_message'] = '页面错误！请稍后再试～';
        $options['bar'] = '发生以下错误：';
        if (is_array($message)) {
            $options['tips'] = $message;
        } else {
            $options['tips'] = array($message);
        }
        $this->setErrorPage($this->request, $options);
        throw new HttpResponseException(Response::create());
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
        if (!is_file($this->extra_path . $this->secureKeyFile)) {
            $secureKey = md5(substr(sha1($default_key), 0, 16));
        } else {
            $secureKeyConfig = json_decode(preg_replace("/\\/\\*[\\s\\S]+?\\*\\//", "", @file_get_contents($this->extra_path . $this->secureKeyFile)), true);
            $secureKeyBase = isset($secureKeyConfig['securekey']) ? $secureKeyConfig['securekey'] : '';
            if (empty($secureKeyBase)) {
                throw new AuthException('No password encryption key specified.');
            }
			
            $key = base64_decode($secureKeyBase);
         
            $secureKey = md5(substr(sha1($key), 0, 16));
        }
        return $secureKey;
    }
    /**
     * 解析数据库连接配置参数
     * @access protected
     * @param  string $hostname 服务器地址
     * @param  string $hostport 端口
     * @param  string $database 数据库名
     * @param  string $username 用户名
     * @param  string $password 密码
     * @param  string $prefix 数据库表前缀
     * @return array
     */
    protected function parseConfig($hostname = null, $hostport = null, $database = null, $username = null, $password = null, $prefix = null) : array
    {
        $config = [
            // 默认数据连接标识
            'default' => 'mysql',
            // 数据库连接信息
            'connections' => ['mysql' => [
                // 数据库类型
                'type' => 'mysql',
                // 服务器地址
                'hostname' => '',
                // 数据库名
                'database' => '',
                // 用户名
                'username' => '',
                // 密码
                'password' => '',
                // 端口
                'hostport' => '',
                // 连接dsn
                'dsn' => '',
                // 数据库连接参数
                'params' => [\PDO::ATTR_EMULATE_PREPARES => true],
                // 数据库编码默认采用utf8
                'charset' => 'utf8',
                // 数据库表前缀
                'prefix' => '',
                // 监听SQL
                'trigger_sql' => true,
            ]],
        ];
        if (!empty($hostname)) {
            $config['connections']['mysql']['hostname'] = $hostname;
        }
        if (!empty($hostport)) {
            $config['connections']['mysql']['hostport'] = $hostport;
        }
        if (!empty($database)) {
            $config['connections']['mysql']['database'] = $database;
        }
        if (!empty($username)) {
            $config['connections']['mysql']['username'] = $username;
        }
        if (!empty($password)) {
            $config['connections']['mysql']['password'] = $password;
        }
        if (!empty($prefix)) {
            $config['connections']['mysql']['prefix'] = $prefix;
        }
        return $config;
    }
    /**
     * 安装许可协议
     * @return  mixed
     */
    public function index()
    {
        // 标识安装步骤，防止跳过某些环节
        session("INSTALLSTEP", 'step2');
        return $this->view->filter(function ($content) {
            return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
        })->fetch(":step1");
    }
    /**
     * 运行环境检测
     * @return  mixed
     */
    public function step2()
    {
        if (session("INSTALLSTEP") == 'step2') {
            $err = 0;
            $server = $this->request->server('SERVER_SOFTWARE');
            if ($this->need_php <= phpversion()) {
                $phpv_str = '<img src="' . $this->static_path . '/images/ok.png">';
            } else {
                $phpv_str = '<img src="' . $this->static_path . '/images/del.png">当前版本(' . phpversion() . ')不支持';
                $err++;
            }
            $safe_mode = ini_get('safe_mode') ? '<img src="' . $this->static_path . '/images/del.png">' : '<img src="' . $this->static_path . '/images/ok.png">';
            $tmp = function_exists('gd_info') ? gd_info() : array();
            if (empty($tmp['GD Version'])) {
                $gd = '<img src="' . $this->static_path . '/images/del.png">';
                $err++;
            } else {
                $gd = '<img src="' . $this->static_path . '/images/ok.png">';
            }
            if (class_exists('pdo')) {
                $pdo = '<img src="' . $this->static_path . '/images/ok.png">';
            } else {
                $pdo = '<img src="' . $this->static_path . '/images/del.png">';
                $err++;
            }
            if (extension_loaded('pdo_mysql')) {
                $pdo_mysql = '<img src="' . $this->static_path . '/images/ok.png">';
            } else {
                $pdo_mysql = '<img src="' . $this->static_path . '/images/del.png">';
                $err++;
            }
            if (function_exists('curl_init')) {
                $curl = '<img src="' . $this->static_path . '/images/ok.png">';
            } else {
                $curl = '<img src="' . $this->static_path . '/images/del.png">';
                $err++;
            }
            $folder = array('runtime', 'app/admin', 'config/database.php');
            $res = array();
            foreach ($folder as $dir) {
                $is_write = false;
                $Testdir = $this->root_path . $dir;
                if (file_exists($Testdir) && is_file($Testdir)) {
                    $is_write = is_writable($Testdir);
                    !empty($is_write) && ($is_write = is_readable($Testdir));
                } else {
                    dir_create($Testdir);
                    $is_write = testwrite($Testdir);
                    !empty($is_write) && ($is_write = is_readable($Testdir));
                }
                if ($is_write) {
                    $w = '<img src="' . $this->static_path . '/images/ok.png">';
                } else {
                    $w = '<img src="' . $this->static_path . '/images/del.png">';
                    $err++;
                }
                $res[$dir] = $w;
            }
            $this->assign(compact('server', 'phpv_str', 'safe_mode', 'gd', 'pdo', 'pdo_mysql', 'curl', 'res', 'err'));
            session("INSTALLSTEP", 'step3');
            return $this->view->filter(function ($content) {
                return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
            })->fetch(":step2");
        }
        return $this->error("非法安装！");
    }
    /**
     * 安装参数设置
     * @return  mixed
     */
    public function step3()
    {
        if (session("INSTALLSTEP") == 'step3') {
            return $this->view->filter(function ($content) {
                return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
            })->fetch(":step3");
        }
        return $this->error("非法安装！");
    }
    /**
     * 检测数据库
     * @return  mixed
     */
    public function testdbpwd()
    {
        if (isAjax()) {
            $database = !empty($this->data['dbName']) ? trim(addslashes($this->data['dbName'])) : '';
            $username = !empty($this->data['dbUser']) ? trim(addslashes($this->data['dbUser'])) : '';
            $hostport = !empty($this->data['dbport']) ? addslashes($this->data['dbport']) : '3306';
            $password = !empty($this->data['dbPwd']) ? $this->data['dbPwd'] : '';
            $hostname = !empty($this->data['dbHost']) ? addslashes($this->data['dbHost']) : '';
            try {
                $dbconfig = $this->parseConfig($hostname, $hostport, null, $username, $password);
                Db::setConfig($dbconfig);
                $result = Db::query("SELECT DATABASE() as name");
                if (empty($database)) {
                    $data = array('errcode' => -2, 'dbpwmsg' => "<span class='green'>信息正确</span>", 'dbnamemsg' => "<span class='red'>数据库不能为空，请设定</span>");
                    return $this->json($data, 200);
                }
            } catch (PDOException $e) {
                $data = array('errcode' => 0, 'dbpwmsg' => "<span for='dbname' generated='true' class='tips_error'>数据库连接失败，请重新设定</span>");
                return $this->json($data, 200);
            }
            // 检测数据库是否存在
            try {
                Db::closedb();
                $dbconfig = $this->parseConfig($hostname, $hostport, $database, $username, $password);
                Db::setConfig($dbconfig);
                $result = Db::query("SELECT DATABASE() as name");
                $data = array('errcode' => 1, 'dbpwmsg' => "<span class='green'>信息正确</span>", 'dbnamemsg' => "<span class='red'>数据库已经存在，系统将覆盖数据库</span>");
                // 标识数据库存在
                session("DATABASE", 'ISEXISTS');
                session("INSTALLSTEP", 'step4');
                return $this->json($data, 200);
            } catch (PDOException $e) {
                $data = array('errcode' => 1, 'dbpwmsg' => "<span class='green'>信息正确</span>", 'dbnamemsg' => "<span class='green'>数据库不存在，系统将自动创建</span>");
                session("DATABASE", 'NOTEXISTS');
                session("INSTALLSTEP", 'step4');
                return $this->json($data, 200);
            }
        }
        return $this->error("非法安装！");
    }
    /**
     * 执行安装过程
     * @return  mixed
     */
    public function step4()
    {
        if (isAjax() && session("INSTALLSTEP") == 'step4') {
            $this->setTimeout(1000);
            $arr = array();
            $hostname = !empty($this->data['dbhost']) ? trim(addslashes($this->data['dbhost'])) : '127.0.0.1';
            $hostport = !empty($this->data['dbport']) ? addslashes($this->data['dbport']) : '3306';
            $database = !empty($this->data['dbname']) ? trim(addslashes($this->data['dbname'])) : 'thinker';
            $username = !empty($this->data['dbuser']) ? trim(addslashes($this->data['dbuser'])) : 'root';
            $password = !empty($this->data['dbpw']) ? trim($this->data['dbpw']) : '';
            $prefix = empty($this->data['dbprefix']) ? 'thinker_' : trim(addslashes($this->data['dbprefix']));
            $adminusername = !empty($this->data['manager']) ? trim(addslashes($this->data['manager'])) : '';
            $adminpassword = !empty($this->data['manager_pwd']) ? trim($this->data['manager_pwd']) : '';
            if (empty(session("DATABASE"))) {
                $arr['code'] = 0;
                $arr['msg'] = "非法安装！";
                return $this->json($arr, 200);
            }
            try {
                $dbconfig = $this->parseConfig($hostname, $hostport, null, $username, $password);
                Db::setConfig($dbconfig);
                $version = Db::query("SELECT VERSION() as version");
                if ($version['0']['version'] < 5.1) {
                    $arr['code'] = 0;
                    $arr['msg'] = '数据库版本(' . $version['0']['version'] . ')太低! 必须 >= 5.1';
                    return $this->json($arr, 200);
                }
                if (session("DATABASE") == 'NOTEXISTS') {
                    $sql = "CREATE DATABASE IF NOT EXISTS `" . $database . "` DEFAULT CHARACTER SET utf8;";
                    $res = Db::execute($sql);
                    if ($res === false) {
                        $arr['code'] = 0;
                        $arr['msg'] = '数据库 ' . $database . ' 不存在，也没权限创建新的数据库，建议联系空间商或者服务器负责人！';
                        return $this->json($arr, 200);
                    }
                }
            } catch (PDOException $e) {
                $arr['code'] = 0;
                $arr['msg'] = "连接数据库失败!" . iconv('gbk', 'utf-8', $e->getMessage());
                return $this->json($arr, 200);
            }
            // 切换链接
            try {
                Db::closedb();
                $dbconfig = $this->parseConfig($hostname, $hostport, $database, $username, $password);
                Db::setConfig($dbconfig);
                $result = Db::query("SELECT DATABASE() as name");
            } catch (PDOException $e) {
                $arr['code'] = 0;
                $arr['msg'] = "连接数据库失败!" . iconv('gbk', 'utf-8', $e->getMessage());
                return $this->json($arr, 200);
            }
            // 读取数据文件
            $sqldata = @file_get_contents($this->extra_path . $this->sqlFile);
            $sqlFormat = sql_split($sqldata, $prefix);
            // 执行SQL语句
            $counts = count($sqlFormat);
            for ($i = 0; $i < $counts; $i++) {
                $sql = trim($sqlFormat[$i]);
                if (strstr($sql, 'CREATE TABLE')) {
                    preg_match('/CREATE TABLE `([^ ]*)`/', $sql, $matches);
                    Db::execute("DROP TABLE IF EXISTS `{$matches[1]}`");
                    $res = Db::execute($sql);
                    if ($res === false) {
                        $message = '创建数据表' . $matches[1] . '失败，请尝试F5刷新!';
                        $arr['code'] = 0;
                        $arr = array('msg' => $message);
                        return $this->json($arr, 200);
                    }
                } else {
                    if (trim($sql) == '') {
                        continue;
                    }
                    preg_match('/INSERT INTO `([^ ]*)`/', $sql, $matches);
                    $res = Db::execute($sql);
                    if ($res === false) {
                        $message = '写入表' . $matches[1] . '记录失败，请尝试F5刷新!';
                        $arr['code'] = 0;
                        $arr = array('msg' => $message);
                        return $this->json($arr, 200);
                    }
                }
            }
            // 清空缓存
            delFile($this->root_path . 'runtime');
            $max_i = 999999999;
            if ($max_i == $i) {
                $arr['code'] = 0;
                $arr['msg'] = "数据库文件过大，执行条数超过{$max_i}条，请联系技术协助！";
                return $this->json($arr, 200);
            }
            $time = getTime();
            // 保存mysql的sql-mode模式参数
            $sql_mode = Db::query("SELECT @@global.sql_mode AS sql_mode");
            $system_sql_mode = isset($sql_mode[0]['sql_mode']) ? $sql_mode[0]['sql_mode'] : '';
            $result = Db::query(" SELECT value FROM `{$prefix}config` WHERE name = 'system_sql_mode' AND inc_type = 'system' LIMIT 1 ");
            if (!empty($result[0]['value'])) {
                Db::execute("UPDATE `{$prefix}config` SET `value` = '{$system_sql_mode}' WHERE name = 'system_sql_mode' AND inc_type = 'system'");
            } else {
                Db::execute(" INSERT INTO `{$prefix}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_sql_mode','{$system_sql_mode}','system','{$time}')");
            }
            // 读取数据库配置文件，并替换真实配置数据
            $strConfig1 = @file_get_contents($this->extra_path . $this->databaseFile);
            $strConfig1 = str_replace('#SQL_MODE#', $system_sql_mode, $strConfig1);
            $strConfig1 = str_replace('#DB_HOST#', $hostname, $strConfig1);
            $strConfig1 = str_replace('#DB_NAME#', $database, $strConfig1);
            $strConfig1 = str_replace('#DB_USER#', $username, $strConfig1);
            $strConfig1 = str_replace('#DB_PWD#', $password, $strConfig1);
            $strConfig1 = str_replace('#DB_PORT#', $hostport, $strConfig1);
            $strConfig1 = str_replace('#DB_PREFIX#', $prefix, $strConfig1);
            $strConfig1 = str_replace('#DB_CHARSET#', 'utf8', $strConfig1);
            @chmod($this->root_path . 'config' . DS . 'database.php', 0777);
            @file_put_contents($this->root_path . 'config' . DS . 'database.php', $strConfig1);
            // 读取环境配置文件，并替换真实配置数据
            $strConfig2 = @file_get_contents($this->extra_path . $this->environmentFile);
            $strConfig2 = str_replace('#SQL_MODE#', $system_sql_mode, $strConfig2);
            $strConfig2 = str_replace('#DB_HOST#', $hostname, $strConfig2);
            $strConfig2 = str_replace('#DB_NAME#', $database, $strConfig2);
            $strConfig2 = str_replace('#DB_USER#', $username, $strConfig2);
            $strConfig2 = str_replace('#DB_PWD#', $password, $strConfig2);
            $strConfig2 = str_replace('#DB_PORT#', $hostport, $strConfig2);
            $strConfig2 = str_replace('#DB_PREFIX#', $prefix, $strConfig2);
            $strConfig2 = str_replace('#DB_CHARSET#', 'utf8', $strConfig2);
            @chmod($this->root_path . $this->environmentFile, 0777);
            @file_put_contents($this->root_path . $this->environmentFile, $strConfig2);
            $web_cmspath = $this->root_dir;
            $web_basehost = $this->request->domain() . $this->root_dir;
            // 更新网站配置的网站网址
            $result = Db::query(" SELECT value FROM `{$prefix}config` WHERE name = 'web_basehost' AND inc_type = 'web' LIMIT 1 ");
            if (!empty($result[0]['value'])) {
                Db::execute("UPDATE `{$prefix}config` SET `value` = '{$web_basehost}' WHERE name = 'web_basehost' AND inc_type = 'web'");
            } else {
                Db::execute(" INSERT INTO `{$prefix}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('web_basehost','{$web_basehost}','web','{$time}')");
            }
            // 更新网站配置的CMS安装路径
            $result = Db::query(" SELECT value FROM `{$prefix}config` WHERE name = 'web_cmspath' AND inc_type = 'web' LIMIT 1 ");
            if (!empty($result[0]['value'])) {
                Db::execute("UPDATE `{$prefix}config` SET `value` = '{$web_cmspath}' WHERE name = 'web_cmspath' AND inc_type = 'web'");
            } else {
                Db::execute(" INSERT INTO `{$prefix}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('web_cmspath','{$web_cmspath}','web','{$time}')");
            }
            // 更新网站配置的CMS版本号
            $result = Db::query(" SELECT value FROM `{$prefix}config` WHERE name = 'system_version' AND inc_type = 'system' LIMIT 1 ");
            if (!empty($result[0]['value'])) {
                Db::execute("UPDATE `{$prefix}config` SET `value` = '{$this->cfg_soft_version}' WHERE name = 'system_version' AND inc_type = 'system'");
            } else {
                Db::execute(" INSERT INTO `{$prefix}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_version','{$this->cfg_soft_version}','system','{$time}')");
            }
            // 密码加密
            $auth_code = $this->secureKey();
            $result = Db::query(" SELECT value FROM `{$prefix}config` WHERE name = 'system_auth_code' AND inc_type = 'system' LIMIT 1 ");
            if (!empty($result[0]['value'])) {
                $auth_code = $result[0]['value'];
            } else {
                Db::execute(" INSERT INTO `{$prefix}config` (`name`,`value`,`inc_type`,`update_time`) VALUES ('system_auth_code','{$auth_code}','system','{$time}')");
            }
            $result = Db::query("SELECT admin_id FROM `{$prefix}admin`");
            if (!empty($result[0]['admin_id'])) {
                // 清空admin表
                Db::execute("truncate table `{$prefix}admin`");
                // 密码加密串，新安装程序，或者没有用户的程序，才给密码加密串
                $result = Db::query("SELECT users_id FROM `{$prefix}users`");
                if (empty($result[0]['users_id'])) {
                    Db::execute("UPDATE `{$prefix}config` SET `value` = '{$auth_code}' WHERE name = 'system_auth_code' AND inc_type = 'system'");
                }
            } else {
                Db::execute("DELETE FROM `{$prefix}admin` WHERE user_name = '{$adminusername}'");
            }
            // 插入管理员表thinker_admin
            $ip = $this->request->clientIP();
            $ip = empty($ip) ? "0.0.0.0" : $ip;
            $adminpassword = md5($auth_code . $adminpassword);
            Db::execute(" INSERT INTO `{$prefix}admin` (`user_name`,`true_name`,`password`,`last_login`,`last_ip`,`login_cnt`,`status`,`add_time`) VALUES ('{$adminusername}','{$adminusername}','{$adminpassword}','0','{$ip}','1','1','{$time}')");
            $url = (string) url("install/Index/step5");
            $arr['code'] = 1;
            $arr['msg'] = "安装成功";
            $arr['url'] = $url;
            session("DATABASE", null);
            session("INSTALLSTEP", 'ISINSTALLED');
            Db::closedb();
            return $this->json($arr, 200);
        }
        return $this->error("非法安装！");
    }
    /**
     * 安装完成
     * @return  mixed
     */
    public function step5()
    {
        if (session("INSTALLSTEP") == 'ISINSTALLED') {
            $service_thinker = 'http://service.thinker.com/api/service/user_push';
            $domain = $this->request->domain(false);
            $host = $this->request->host(true);
            $cms_version = $this->cfg_soft_version;
            $time = getTime();
            $mt_rand_str = date("Ymdhis") . $this->secureKey();
            $ip = $this->request->serverIP();
            $phpv = urlencode(phpversion());
            $web_server = urlencode($this->request->server('SERVER_SOFTWARE'));
            $this->assign(compact('service_thinker', 'domain', 'host', 'cms_version', 'time', 'mt_rand_str', 'ip', 'phpv', 'web_server'));
            $confDir = $this->root_path . 'app' . DS . 'admin' . DS . 'config' . DS;
            if (!is_dir($confDir)) {
                @mkdir($confDir, 0777, true);
            }
            @file_put_contents($confDir . 'constant.php', "<?php" . PHP_EOL . "define('INSTALL_DATE'," . $time . ");" . PHP_EOL . "define('SERIALNUMBER','" . $mt_rand_str . "');");
            $html = $this->view->filter(function ($content) {
                return str_replace("{__INSTALL_PATH__}", $this->static_path, $content);
            })->fetch(":step5");
            @touch($this->extra_path . 'install.lock');
            session("INSTALLSTEP", null);
            return $html;
        }
        return $this->error("非法安装！");
    }
}