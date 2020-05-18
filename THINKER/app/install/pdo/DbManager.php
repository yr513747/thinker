<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace app\install\pdo;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\SimpleCache\CacheInterface;
use app\install\pdo\db\BaseQuery;
use app\install\pdo\db\ConnectionInterface;
use app\install\pdo\db\Query;
use app\install\pdo\db\Raw;
use think\Log;
use think\Cache;

/**
 * 克隆自thinkphp6.0.2并进行了简化仅用于程序安装使用！！！ by 仰融
 * Class DbManager
 * @package think
 * @mixin BaseQuery
 * @mixin Query
 */
class DbManager
{
	/**
     * 数据库连接实例
     * @var array
     */
    protected $instance = [];

    /**
     * 数据库配置
     * @var array
     */
    protected $config = [];

    /**
     * Event对象或者数组
     * @var array|object
     */
    protected $event;

    /**
     * SQL监听
     * @var array
     */
    protected $listen = [];

    /**
     * SQL日志
     * @var array
     */
    protected $dbLog = [];

    /**
     * 查询次数
     * @var int
     */
    protected $queryTimes = 0;

    /**
     * 查询缓存对象
     * @var CacheInterface
     */
    protected $cache;

    /**
     * 查询日志对象
     * @var LoggerInterface
     */
    protected $log;

    /**
     * 架构函数
     * @access public
     */
    public function __construct()
    {
        
    }
	
	/**
	 * 初始化本类
     * @param Log    $log
     * @param Cache  $cache
     * @return Db
     * @codeCoverageIgnore
     */
    public static function __make(Log $log, Cache $cache)
    {
        $db = new static();
        $db->setLog($log);
        $db->setCache($cache);
        $db->triggerSql();

        return $db;
    }
	
	/**
     * 关闭链接用于在同一组配置中切换不同的链接参数by仰融
     * @access public
     * @return void
     */
    public function closedb(): void
    {
		$this->instance = [];
        $this->config   = [];
		$this->event    = null;
		$this->listen   = [];
		$this->dbLog    = [];
		$this->cache    = null;
		$this->log      = null;
    }

    /**
     * 监听SQL
     * @access protected
     * @return void
     */
    protected function triggerSql(): void
    {
        // 监听SQL
        $this->listen(function ($sql, $time, $master) {
            if (0 === strpos($sql, 'CONNECT:')) {
                $this->log($sql);
                return;
            }

            // 记录SQL
            if (is_bool($master)) {
                // 分布式记录当前操作的主从
                $master = $master ? 'master|' : 'slave|';
            } else {
                $master = '';
            }

            $this->log($sql . ' [ ' . $master . 'RunTime:' . $time . 's ]');
        });
    }

    /**
     * 初始化配置参数
     * @access public
     * @param array $config 连接配置
     * @return void
     */
    public function setConfig($config): void
    {
        $this->config = $config;
    }

    /**
     * 设置缓存对象
     * @access public
     * @param CacheInterface $cache 缓存对象
     * @return void
     */
    public function setCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * 设置日志对象
     * @access public
     * @param LoggerInterface $log 日志对象
     * @return void
     */
    public function setLog(LoggerInterface $log): void
    {
        $this->log = $log;
    }

    /**
     * 记录SQL日志
     * @access protected
     * @param string $log  SQL日志信息
     * @param string $type 日志类型
     * @return void
     */
    public function log(string $log, string $type = 'sql')
    {
        if ($this->log) {
            $this->log->log($type, $log);
        } else {
            $this->dbLog[$type][] = $log;
        }
    }

    /**
     * 获得查询日志（没有设置日志对象使用）
     * @access public
     * @param bool $clear 是否清空
     * @return array
     */
    public function getDbLog(bool $clear = false): array
    {
        $logs = $this->dbLog;
        if ($clear) {
            $this->dbLog = [];
        }

        return $logs;
    }

    /**
     * 获取配置参数
     * @access public
     * @param string $name    配置参数
     * @param mixed  $default 默认值
     * @return mixed
     */
    public function getConfig(string $name = '', $default = null)
    {
        if ('' === $name) {
            return $this->config;
        }

        return $this->config[$name] ?? $default;
    }

    /**
     * 创建/切换数据库连接查询
     * @access public
     * @param string|null $name  连接配置标识
     * @param bool        $force 强制重新连接
     * @return BaseQuery
     */
    public function connect(string $name = null, bool $force = false): BaseQuery
    {
        $connection = $this->instance($name, $force);

        $class = $connection->getQueryClass();
        $query = new $class($connection);

        $timeRule = $this->getConfig('time_query_rule');
        if (!empty($timeRule)) {
            $query->timeRule($timeRule);
        }

        return $query;
    }

    /**
     * 创建数据库连接实例
     * @access protected
     * @param string|null $name  连接标识
     * @param bool        $force 强制重新连接
     * @return ConnectionInterface
     */
    protected function instance(string $name = null, bool $force = false): ConnectionInterface
    {
        if (empty($name)) {
            $name = $this->getConfig('default', 'mysql');
        }

        if ($force || !isset($this->instance[$name])) {
            $this->instance[$name] = $this->createConnection($name);
        }

        return $this->instance[$name];
    }

    /**
     * 获取连接配置
     * @param string $name
     * @return array
     */
    protected function getConnectionConfig(string $name): array
    {
        $connections = $this->getConfig('connections');
        if (!isset($connections[$name])) {
            throw new InvalidArgumentException('Undefined db config:' . $name);
        }

        return $connections[$name];
    }

    /**
     * 创建连接
     * @param $name
     * @return ConnectionInterface
     */
    protected function createConnection(string $name): ConnectionInterface
    {
        $config = $this->getConnectionConfig($name);

        $type = !empty($config['type']) ? $config['type'] : 'mysql';

        if (false !== strpos($type, '\\')) {
            $class = $type;
        } else {
            $class = '\\app\\install\\pdo\\db\\connector\\' . ucfirst($type);
        }

        /** @var ConnectionInterface $connection */
        $connection = new $class($config);
        $connection->setDb($this);

        if ($this->cache) {
            $connection->setCache($this->cache);
        }

        return $connection;
    }

    /**
     * 使用表达式设置数据
     * @access public
     * @param string $value 表达式
     * @return Raw
     */
    public function raw(string $value): Raw
    {
        return new Raw($value);
    }

    /**
     * 更新查询次数
     * @access public
     * @return void
     */
    public function updateQueryTimes(): void
    {
        $this->queryTimes++;
    }

    /**
     * 重置查询次数
     * @access public
     * @return void
     */
    public function clearQueryTimes(): void
    {
        $this->queryTimes = 0;
    }

    /**
     * 获得查询次数
     * @access public
     * @return integer
     */
    public function getQueryTimes(): int
    {
        return $this->queryTimes;
    }

    /**
     * 监听SQL执行
     * @access public
     * @param callable $callback 回调方法
     * @return void
     */
    public function listen(callable $callback): void
    {
        $this->listen[] = $callback;
    }

    /**
     * 获取监听SQL执行
     * @access public
     * @return array
     */
    public function getListen(): array
    {
        return $this->listen;
    }

    /**
     * 注册回调方法
     * @access public
     * @param string   $event    事件名
     * @param callable $callback 回调方法
     * @return void
     */
    public function event(string $event, callable $callback): void
    {
        $this->event[$event][] = $callback;
    }

    /**
     * 触发事件
     * @access public
     * @param string $event  事件名
     * @param mixed  $params 传入参数
     * @return mixed
     */
    public function trigger(string $event, $params = null)
    {
        if (isset($this->event[$event])) {
            foreach ($this->event[$event] as $callback) {
                call_user_func_array($callback, [$this]);
            }
        }
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->connect(), $method], $args);
    }
}
