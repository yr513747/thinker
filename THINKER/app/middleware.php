<?php
// 全局中间件定义文件
return [
    // 全局请求缓存
    // \think\middleware\CheckRequestCache::class,
    // 多语言加载
    // \think\middleware\LoadLangPack::class,
    // Session初始化
     \think\middleware\SessionInit::class,
	// 检测系统是否安装
	\app\common\middleware\CheckAndSetupIsInstalledMiddleware::class,
	// 关闭网站友好提示页面
	\app\common\middleware\CheckWebStatusMiddleware::class,
	// 系统环境检查
	\app\common\middleware\SystemEnvCheckMiddleware::class,
];
