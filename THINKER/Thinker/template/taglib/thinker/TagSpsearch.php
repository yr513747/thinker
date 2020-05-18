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
// [ 会员中心搜索表单 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagSpsearch extends Base
{
    public function getSpsearch()
    {
        $hidden = '';
        // 搜索的URL
        $searchurl = url('user/Shop/shop_centre');
        $result[0] = array(
            //
            'action' => $searchurl,
            'hidden' => $hidden,
        );
        return $result;
    }
}