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
// [ arclist列表分页标签 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagArcpagelist extends Base
{
    /**
     * 获取ajax分页
     * @access    public
     * @param     string  $tagid  标签id
     * @param     string  $pagesize  分页显示条数
     * @return    array
     */
    public function getArcpagelist($tagid = '', $pagesize = 0, $tips = '', $loading = '', $callback = '', $arclistTag = [])
    {
        if (empty($tagid)) {
            return '标签arcpagelist报错：缺少属性 tagid 。';
        }
        empty($tips) && ($tips = '没有数据了');
        $tagidmd5 = $tagid . '_' . md5(serialize($arclistTag));
		$arcmultiRow = Db::name('arcmulti')->field('pagesize,attstr,querysql')->where(['tagid' => $tagidmd5])->getOne();
        if (empty($pagesize)) {
            $pagesize = $arcmultiRow['pagesize'];
        }      
        if (empty($arcmultiRow)) {
            return false;
        } else {
            // 取出属性并解析为变量
            $attarray = unserialize(stripslashes($arcmultiRow['attstr']));
            $querysql = preg_replace('#LIMIT(\\s+)(\\d+)(,\\d+)?#i', '', $arcmultiRow['querysql']);
            $querysql = preg_replace('#SELECT(\\s+)(.*)(\\s+)FROM#i', 'SELECT COUNT(*) AS totalNum FROM', $querysql);
            $queryRow = Db::query($querysql);
            $totalNum = !empty($queryRow) ? $queryRow[0]['totalNum'] : 0;
            if (intval($attarray['row']) >= $totalNum) {
                return false;
            }
        }
        $result = [];
        $version = $this->params['version'];
        $result['onclick'] = ' data-page="1" data-tips="' . $tips . '" data-loading="' . $loading . '" data-root_dir="' . $this->root_dir . '" data-tagidmd5="' . $tagidmd5 . '"  onClick="tag_arcpagelist_multi(this,\'' . $tagid . '\',' . intval($pagesize) . ',\'' . $callback . '\');" ';
        $result['js'] = <<<EOF
<script type="text/javascript" src="{$this->root_dir}/static/common/js/tag_arcpagelist.js?v={$version}"></script>
EOF;
        return $result;
    }
}