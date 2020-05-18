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
// [ 数据库配置文件 ]
// --------------------------------------------------------------------------

return [
    // 默认使用的数据库连接配置
    'default'         => env('database.driver', 'mysql'),
    // 自定义时间查询规则
    'time_query_rule' => [],
    /**
     * 自动写入时间戳字段
     * true为自动识别类型 false关闭
     * 字符串则明确指定时间字段类型 支持 int timestamp datetime date
     */
    'auto_timestamp'  => true,
    // 时间字段取出后的默认时间格式
    'datetime_format' => 'Y-m-d H:i:s',
    // mysql的sql-mode模式参数
    'system_sql_mode' => env('database.sqlmode', 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'),
    // 数据库连接配置信息
    'connections'     => [
        'mysql' => [
            // 数据库类型
            'type'              => env('database.type', 'mysql'),
            // 服务器地址
            'hostname'          => env('database.hostname', '127.0.0.1'),
            // 数据库名
            'database'          => env('database.database', 'thinker'),
            // 用户名
            'username'          => env('database.username', 'root'),
            // 密码
            'password'          => env('database.password', 'dedecms'),
            // 端口
            'hostport'          => env('database.hostport', '3306'),
            // 连接dsn
            'dsn'               => '',
            // 数据库连接参数
            'params'            => [
			    \PDO::ATTR_CASE             => \PDO::CASE_LOWER,
		        \PDO::ATTR_EMULATE_PREPARES => true
		    ],
            // 数据库编码默认采用utf8mb4
            'charset'           => env('database.charset', 'utf8mb4'),
            // 数据库表前缀
            'prefix'            => env('database.prefix', 'tp_'),
            // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
            'deploy'            => 0,
            // 数据库读写是否分离 主从式有效
            'rw_separate'       => false,
            // 读写分离后 主服务器数量
            'master_num'        => 1,
            // 指定从服务器序号
            'slave_no'          => '',
            // 模型写入后自动读取主服务器
            'read_master'       => false,
            // 是否严格检查字段是否存在
            'fields_strict'     => true,
            // 开启字段缓存
            'fields_cache'      => true,
            // 监听SQL
            'trigger_sql'       => env('app_debug', false),
            // Builder类
            'builder'           => '',
            // Query类
            'query'             => '\\core\\db\\Query',
            // 是否需要断线重连
            'break_reconnect'   => false,
            // 断线标识字符串
            'break_match_str'   => [],
            // 字段缓存路径
            'schema_cache_path' => runtime_path('schema'),
        ],
		
		// 更多的数据库配置信息
    ],
];