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
// [ 前台控制器基类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use app\common\controller\Common;
use app\home\logic\FieldLogic;
abstract class BaseController extends Common
{
    protected $fieldLogic;
    /**
     * 初始化操作
     * @access protected
     */
    protected function initialize()
    {
        parent::initialize();
        $this->fieldLogic = new FieldLogic();
    }
    /**
     * 301重定向到新的伪静态格式（针对被搜索引擎收录的旧伪静态URL）
     * @access protected
     * @param intval $id 栏目ID/文档ID
     * @param string $dirname 目录名称
     * @param string $type 栏目页/文档页
     * @return void
     */
    protected function jumpRewriteFormat($id, $dirname = null, $type = 'lists')
    {
        if ('lists' == $type) {
            $url = typeurl('home/Lists/index', array('dirname' => $dirname));
        } else {
            $url = arcurl('home/View/index', array('dirname' => $dirname, 'aid' => $id));
        }
        //重定向到指定的URL地址 并且使用301
        return $this->redirect($url, 301);
    }
}