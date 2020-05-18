<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

return [
    // 应用地址
    'app_host'            => env('app.host', ''),

    // +------------------------------------------------------------------
    // |  加密密钥
    // +------------------------------------------------------------------
    // | 
    // |  此密钥由inc encrypter服务使用，应设置
    // |  一个随机的，32个字符的字符串，否则这些加密的字符串
    // |  不会安全的。请在部署应用程序之前执行此操作！
    // +------------------------------------------------------------------
    'app_key'                 => env('app_key', ''),
    'cipher'              => 'AES-256-CBC',

    // 应用的命名空间
    'app_namespace'       => '',
    // 是否启用路由
    'with_route'          => false,
    // 默认应用
    'default_app'         => 'admin',
    // 默认时区
    'default_timezone'    => 'Asia/Shanghai',

    // 应用映射（自动多应用模式有效）
    'app_map'             => [],
    // 域名绑定（自动多应用模式有效）
    'domain_bind'         => [],
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'       => ['common'],
	 // 异常页面的模板文件
    'exception_tmpl'      => root_path('errorpage').'tpl/think_exception.tpl',
    // errorpage 错误页面
    'error_tmpl'          => root_path('errorpage').'tpl/think_error.tpl',
	// 默认操作成功跳转对应的模板文件
    'jump_success_tmpl'   => root_path('errorpage').'tpl/dispatch_jump.tpl',
    // 默认操作失败跳转页面对应的模板文件
    'jump_error_tmpl'     => root_path('errorpage').'tpl/dispatch_jump.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'       => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'      => true,
	// 手工开启php服务器错误报告
	'web_exception'       => true,
	// +----------------------------------------------------------------------
    // | 自定义设置
    // +----------------------------------------------------------------------
	// 默认全局过滤方法 用逗号分隔多个
    'default_filter'      => [],
	// 应用类库后缀
    'class_suffix'        => false,		
	// 注册的根命名空间
    'root_namespace'      => [],
	// 禁止index.php访问的应用列表（多应用模式有效）,适用于诸如admin后台模块之类
    'deny_multi_app_list' => ['admin'],
	// 是否支持多应用
    'multi_app'           => true,
	// 入口自动绑定应用
    'auto_bind_app'       => false,
    // 默认验证器
    'default_validate'    => '',
    // 默认的空应用名（自动多应用模式有效）
    'empty_app'           => '',
   
];
