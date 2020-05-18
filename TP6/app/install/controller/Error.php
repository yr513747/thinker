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
// [ 空控制器响应 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\install\controller;

class Error extends Index
{
    /**
     * 空控制器响应
     * @access public
     */
    public function index()
    {
        $this->assign('msg', lang('controller not exists') . ':' . $this->params['controller_name']);
        return $this->fetch('public/error');
    }
}