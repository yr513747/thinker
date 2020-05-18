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
// [ 搜索表单 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagSearchform extends Base
{
    public function getSearchform($typeid = '', $channelid = '', $notypeid = '', $flag = '', $noflag = '', $type = '')
    {
        $searchurl = url('home/Search/lists');
        $hidden = '';
        !empty($typeid) && ($hidden .= '<input type="hidden" name="typeid" id="typeid" value="' . $typeid . '" />');
        !empty($channelid) && ($hidden .= '<input type="hidden" name="channelid" id="channelid" value="' . $channelid . '" />');
        !empty($notypeid) && ($hidden .= '<input type="hidden" name="notypeid" id="notypeid" value="' . $notypeid . '" />');
        !empty($flag) && ($hidden .= '<input type="hidden" name="flag" id="flag" value="' . $flag . '" />');
        !empty($noflag) && ($hidden .= '<input type="hidden" name="noflag" id="noflag" value="' . $noflag . '" />');
        !empty($type) && 'default' != $type && ($hidden .= '<input type="hidden" name="type" id="type" value="' . $type . '" />');
        $result[0] = array('searchurl' => $searchurl, 'action' => $searchurl, 'hidden' => $hidden);
        return $result;
    }
}