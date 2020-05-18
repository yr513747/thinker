<?php
// 事件定义文件
return [
    // 绑定标识
    'bind' => [],
    // 事件监听
    'listen' => [
        // 应用初始化
        'AppInit' => [],
		// 应用开始
        'HttpRun' => [
            // 设置动态数据
            '\\app\\common\\listener\\ConfigSetupListener',
            // 路由规则处理
            '\\app\\common\\listener\\RouteSeparatorHandleListener',
            // 保存mysql的sql-mode模式参数
            '\\app\\common\\listener\\SqlModeListener',
        ],
		// 路由加载完成
		'RouteLoaded' => [],
		// 操作执行
        'ActionBegin' => [],
		// 应用结束
        'HttpEnd' => [],
		// 日志write
        'LogLevel' => [],
        'LogWrite' => [],
		// 视图过滤
        'ViewFilter' => [],
    ],
    // 事件订阅
    'subscribe' => [],
];