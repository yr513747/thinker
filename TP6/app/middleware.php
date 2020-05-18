<?php
// 全局中间件定义文件
return [
    // 全局请求缓存
    //'\\think\\middleware\\CheckRequestCache',
    // 多语言加载
    //'\\think\\middleware\\LoadLangPack',
    // Session初始化
    '\\think\\middleware\\SessionInit',
    // 检测系统是否安装
    '\\app\\common\\middleware\\SystemIsInstalledCheckMiddleware',
    // 关闭网站友好提示页面
    '\\app\\common\\middleware\\SystemWebStatusCheckMiddleware',
    // 系统环境检查
    '\\app\\common\\middleware\\SystemEnvCheckMiddleware',
];