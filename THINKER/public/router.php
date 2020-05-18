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
// [ 快速测试入口文件 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker;

// 载入基础配置文件
$ThinkerBaseFile = realpath(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'Thinker' . DIRECTORY_SEPARATOR . 'base.php';
if (!is_file($ThinkerBaseFile)) {
    exit("Unable to load the requested file:" . $ThinkerBaseFile);
} else {
    require $ThinkerBaseFile;
}
// 应用根目录
define('ROOT_PATH', null);
// 请求对象
define('REQUEST_OBJECT', null);
// 应用名
define('APP_NAME', null);
// 执行应用
AppLication::runWithSubObject('text');
/*----------------------------------------------------------------------|||分割线，以下为代码测试区域|||----------------------------------------------------------------------*/