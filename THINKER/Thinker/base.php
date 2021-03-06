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
// [ 框架基础配置文件 ]
// --------------------------------------------------------------------------
namespace Thinker;

// +-------------------------------------------------------------------------
// [ 运营模式请设置此项为false ]
// --------------------------------------------------------------------------
define('DEBUG_LEVEL', TRUE);
// +-------------------------------------------------------------------------
// [ 支付宝 - 是否处于开发模式 ]
// +-------------------------------------------------------------------------
defined('AOP_SDK_DEV_MODE') or define('AOP_SDK_DEV_MODE', false);
// +-------------------------------------------------------------------------
// [ 间隔符 ]
// --------------------------------------------------------------------------
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
// +-------------------------------------------------------------------------
// | 全局变量数组
// +-------------------------------------------------------------------------
global $_M;
// +-------------------------------------------------------------------------
// | PHP 关键词
// +-------------------------------------------------------------------------
$get_defined_functions = array('__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor');
// +-------------------------------------------------------------------------
// | 编译时常量
// +-------------------------------------------------------------------------
$get_defined_constants = array('__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__');
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
// +-------------------------------------------------------------------------
// | 软件摘要信息，****请不要删除本项**** 否则系统无法正确接收系统漏洞或升级信息
// +-------------------------------------------------------------------------
$cfg_soft_version = 'v1.3.1';
$cfg_soft_lang = 'utf-8';
$cfg_soft_public = 'base';
$cfg_soft_name = 'Thinker内容管理系统';
$cfg_soft_enname = 'THINKERCMS';
$cfg_soft_devteam = 'THINKER官方团队';
// +-------------------------------------------------------------------------
// | 安装程序定义
// +-------------------------------------------------------------------------
define('DEFAULT_INSTALL_DATE', 1586690572);
// +-------------------------------------------------------------------------
// | 序列号
// +-------------------------------------------------------------------------
define('DEFAULT_SERIALNUMBER', '20200412072200oCWIoa');
// +-------------------------------------------------------------------------
// | 缓存时间
// +-------------------------------------------------------------------------
defined('CACHE_TIME') or define('CACHE_TIME', 86400);
// +-------------------------------------------------------------------------
// | 表单变量自动过滤
// +-------------------------------------------------------------------------
defined('MAGIC_QUOTES_GPC') or define('MAGIC_QUOTES_GPC', @get_magic_quotes_gpc());
// +-------------------------------------------------------------------------
// | 编辑器图片上传相对路径
// +-------------------------------------------------------------------------
defined('UPLOAD_NAME') or define('UPLOAD_NAME', 'uploads');
defined('UPLOAD_PATH') or define('UPLOAD_PATH', UPLOAD_NAME . DS);
// +-------------------------------------------------------------------------
// | 装载自动加载函数
// +-------------------------------------------------------------------------
$vendorAutoLoadFile = realpath(dirname(dirname(__FILE__))) . DS . 'vendor' . DS . 'autoload.php';
if (!is_file($vendorAutoLoadFile)) {
    exit("Unable to load the requested file:" . $vendorAutoLoadFile);
} else {
    require $vendorAutoLoadFile;
}
@php_strip_whitespace(__FILE__);
// --------------------------------------------------------------------------