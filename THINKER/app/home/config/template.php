<?php
// +----------------------------------------------------------------------
// | 模板设置
// +----------------------------------------------------------------------

return [
        // 模板引擎类型使用Think
        'type'          => 'Think',
		// 0 = 响应式模板，1 = 分离式模板
        'response_type' => 0,
        // 0 = 响应式手机端，1 = 分离式手机端
        'separate_mobile' => 0,
		// 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule'          => 1,
	    // 模板目录名
        'view_dir_name'      => 'view',  
		// 模板路径
        'view_path'          => root_path('view'), 
		 // 默认模板文件后缀
        'view_suffix'        => 'htm',
		// 默认模板文件分隔符
        'view_depr'          => DIRECTORY_SEPARATOR,
		//默认模板缓存路径
        'cache_path'         => runtime_path('temp'),
		// 默认模板缓存后缀
        'cache_suffix'       => 'php', 
		// 模板引擎禁用函数
        'tpl_deny_func_list' => 'echo,exit', 
		// 默认模板引擎是否禁用PHP原生代码
        'tpl_deny_php'       => false, 
		// 模板引擎普通标签开始标记
        'tpl_begin'          => '{', 
		// 模板引擎普通标签结束标记
        'tpl_end'            => '}', 
	    // 是否去除模板文件里面的html空格与换行
        'strip_space'        => false,
		// 是否开启模板编译缓存,设为false则每次都会重新编译
        'tpl_cache'          => false, 
		// 模板编译类型
        'compile_type'       => 'file', 
		// 模板缓存前缀标识，可以动态改变
        'cache_prefix'       => '', 
		// 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
        'cache_time'         => 0, 
		// 布局模板开关
        'layout_on'          => false, 
		 // 布局模板入口文件
        'layout_name'        => 'layout',
		// 布局模板的内容替换标识
        'layout_item'        => '{__CONTENT__}', 
		// 标签库标签开始标记
        'taglib_begin'       => '{', 
		// 标签库标签结束标记
        'taglib_end'         => '}', 
		// 是否使用内置标签库之外的其它标签库，默认自动检测
        'taglib_load'        => true, 
		// 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔 注意解析顺序
        'taglib_build_in'    => 'thinker', 
		// 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
        'taglib_pre_load'    => '', 
		// 模板渲染缓存
        'display_cache'      => false, 
		// 模板缓存ID
        'cache_id'           => '', 
		// .语法变量识别，array|object|'', 为空时自动识别 
        'tpl_var_identify'   => 'array', 
		// 默认过滤方法 用于普通标签输出
		//'default_filter'     => ['trim','strip_sql','htmlentities','html_entity_decode','htmlspecialchars','htmlspecialchars_decode'],
		'default_filter'     => ['trim','strip_sql','htmlspecialchars_decode'],
		// +---------------------------------------------------------------------
        // | 视图输出字符串内容替换
        // +---------------------------------------------------------------------
        'view_replace_str' => array(
        // 过滤模板里的eval函数，防止被注入
        '__EVAL__' => '',
        ),
		// 模板视图内容替换
		'tpl_replace_string' => [],
];
