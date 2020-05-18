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
// [ Ajax异步类 ]
// --------------------------------------------------------------------------
namespace app\api\controller;

use think\facade\Db;
class Other extends BaseController
{
    /**
     * 广告位js
     * @access public
     * @return mixed
     */
    public function otherShow()
    {
        $pid = input('pid/d', 1);
        $row = input('row/d', 1);
        $ad = Db::name("ad")
		->where('pid', $pid)
		->where('status', 1)
		->where('start_time', '<', getTime())
		->whereRaw('`end_time` > ' . getTime() . ' OR `end_time` = 0')
		->order("sort_order asc")
		->limit((int) $row)
		->cache(true, CACHE_TIME, 'ad')
		->select();
        $this->assign('ad', $ad);
        return $this->fetch();
    }
}