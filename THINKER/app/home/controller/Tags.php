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
// [ 标签 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use think\facade\Db;
class Tags extends BaseController
{
    /**
     * 标签主页
     */
    public function index()
    {
        // 获取当前页面URL
        $result['pageurl'] = url('home/Tags/index');
        $thinker = array('field' => $result);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
        return $this->fetch(":index_tags");
    }
    /**
     * 标签列表
     */
    public function lists()
    {
        $tagid = isset($this->data['tagid']) ? $this->data['tagid'] : '';
        $tag = isset($this->data['tag']) ? $this->data['tag'] : '';
        if (!empty($tag)) {
            $tagindexInfo = Db::name('tagindex')->where(['tag' => $tag])->getOne();
        } elseif (intval($tagid) > 0) {
            $tagindexInfo = Db::name('tagindex')->where(['id' => $tagid])->getOne();
        }
        if (!empty($tagindexInfo)) {
            $tagid = $tagindexInfo['id'];
            $tag = $tagindexInfo['tag'];
            //更新浏览量和记录数
            $total = Db::name('taglist')->where('tid', '=', $tagid)->where('arcrank', '>', -1)->count('tid');
            Db::name('tagindex')->where(['id' => $tagid])->inc('count')->inc('weekcc')->inc('monthcc')->update(array('total' => $total));
            $ntime = getTime();
            $oneday = 24 * 3600;
            //周统计
            if (ceil(($ntime - $tagindexInfo['weekup']) / $oneday) > 7) {
                Db::name('tagindex')->where(['id' => $tagid])->update(array('weekcc' => 0, 'weekup' => $ntime));
            }
            //月统计
            if (ceil(($ntime - $tagindexInfo['monthup']) / $oneday) > 30) {
                Db::name('tagindex')->where(['id' => $tagid])->update(array('monthcc' => 0, 'monthup' => $ntime));
            }
        }
        $field_data = array('tag' => $tag, 'tagid' => $tagid);
        $thinker = array('field' => $field_data);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
        return $this->fetch(":lists_tags");
    }
}