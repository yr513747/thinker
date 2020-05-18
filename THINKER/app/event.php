<?php
// 事件定义文件
return [
    'bind'      => [
    ],

    'listen'    => [
        'AppInit'     => [],
        'HttpRun'     => [
		// 设置动态数据
		\app\common\listener\ConfigSetupListener::class,
		// 路由规则处理
		\app\common\listener\RouteSeparatorHandleListener::class,
		// 保存mysql的sql-mode模式参数
		\app\common\listener\SqlModeListener::class,
		],
        'HttpEnd'     => [],
        'LogLevel'    => [],
        'LogWrite'    => [],
		'ViewFilter'  => [],
    ],

    'subscribe' => [
    ],
];
