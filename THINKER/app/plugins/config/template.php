<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
    // 模板后缀
    'view_suffix'        => 'htm',
    // 模板路径
    'view_path'          => root_path('view/plugins'),
	// 模板引擎禁用函数
    'tpl_deny_func_list' => 'eval,echo,exit',
    // 默认模板引擎是否禁用PHP原生代码 
    'tpl_deny_php'       => false,
	// 视图输出字符串内容替换
    'view_replace_str' => array(
        '__EVAL__'  => '', // 过滤模板里的eval函数，防止被注入
    ),
];
