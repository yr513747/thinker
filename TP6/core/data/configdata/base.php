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
// [ 常规配置 ]
// --------------------------------------------------------------------------
// +-------------------------------------------------------------------------
// | 软件摘要信息
// +-------------------------------------------------------------------------
$base = [
    // ****请不要删除本项**** 否则系统无法正确接收系统漏洞或升级信息
    'cfg_soft_version' => 'v1.3.1',
    'cfg_soft_lang'    => 'utf-8',
    'cfg_soft_public'  => 'base',
    'cfg_soft_name'    => 'Thinker内容管理系统',
    'cfg_soft_enname'  => 'THINKERCMS',
    'cfg_soft_devteam' => 'THINKER官方团队',
];
// +-------------------------------------------------------------------------
// | PHP 关键词
// +-------------------------------------------------------------------------
$get_defined_functions = [
    // TODOMORE
    '__halt_compiler',
    'abstract',
    'and',
    'array',
    'as',
    'break',
    'callable',
    'case',
    'catch',
    'class',
    'clone',
    'const',
    'continue',
    'declare',
    'default',
    'die',
    'do',
    'echo',
    'else',
    'elseif',
    'empty',
    'enddeclare',
    'endfor',
    'endforeach',
    'endif',
    'endswitch',
    'endwhile',
    'eval',
    'exit',
    'extends',
    'final',
    'for',
    'foreach',
    'function',
    'global',
    'goto',
    'if',
    'implements',
    'include',
    'include_once',
    'instanceof',
    'insteadof',
    'interface',
    'isset',
    'list',
    'namespace',
    'new',
    'or',
    'print',
    'private',
    'protected',
    'public',
    'require',
    'require_once',
    'return',
    'static',
    'switch',
    'throw',
    'trait',
    'try',
    'unset',
    'use',
    'var',
    'while',
    'xor',
];
// +-------------------------------------------------------------------------
// | 编译时常量
// +-------------------------------------------------------------------------
$get_defined_constants = [
    // TODOMORE
    '__CLASS__',
    '__DIR__',
    '__FILE__',
    '__FUNCTION__',
    '__LINE__',
    '__METHOD__',
    '__NAMESPACE__',
    '__TRAIT__',
];
// +-------------------------------------------------------------------------
// | 已定义变量列表的多维数组，这些变量包括环境变量、服务器变量和用户定义的变量。
// +-------------------------------------------------------------------------
$get_defined_vars = @get_defined_vars();
// +-------------------------------------------------------------------------
// | PHP内置扩展组件以及相关函数名
// +-------------------------------------------------------------------------
$get_loaded_extensions = @get_loaded_extensions();
foreach ($get_loaded_extensions as $_get_loaded_extensions) {
    $get_extension_funcs[$_get_loaded_extensions] = @get_extension_funcs($_get_loaded_extensions);
}
return compact('base', 'get_defined_functions', 'get_defined_constants', 'get_defined_vars', 'get_loaded_extensions', 'get_extension_funcs');
// --------------------------------------------------------------------------