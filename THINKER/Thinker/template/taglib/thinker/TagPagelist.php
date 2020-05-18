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
// [ 获取列表分页代码 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagPagelist extends Base
{
    public function getPagelist($pages = '', $listitem = '', $listsize = '')
    {
        if (empty($pages)) {
            echo '标签pagelist报错：只适用在标签list之后。';
            return false;
        }
        $listitem = !empty($listitem) ? $listitem : 'info,index,end,pre,next,pageno';
        $listsize = !empty($listsize) ? $listsize : '3';
        $value = $pages->render($listitem, $listsize);
        return $value;
    }
}