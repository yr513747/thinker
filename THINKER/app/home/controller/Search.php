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
// [ 搜索 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use think\facade\Db;
class Search extends BaseController
{
    /**
     * 搜索主页
     */
    public function index()
    {
        return $this->lists();
    }
    /**
     * 搜索列表
     */
    public function lists()
    {
        /*记录搜索词*/
        $word = isset($this->data['keywords']) ? $this->data['keywords'] : '';
        $page = isset($this->data['page']) ? $this->data['page'] : 0;
        if (!empty($word) && 2 > $page) {
            $nowTime = getTime();
            $row = Db::name('search_word')->field('id')->where(['word' => $word])->getOne();
            if (empty($row)) {
                Db::name('search_word')->insert([
                    //
                    'word' => $word,
                    'sort_order' => 100,
                    'add_time' => $nowTime,
                    'update_time' => $nowTime,
                ]);
            } else {
                Db::name('search_word')->where(['id' => $row['id']])->update([
                    //
                    'searchNum' => Db::raw('searchNum+1'),
                    'update_time' => $nowTime,
                ]);
            }
        }
        /*--end*/
        $result = $this->data;
        $result['keywords'] = isset($result['keywords']) ? $result['keywords'] : '';
        $thinker = array('field' => $result);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
        return $this->fetch(":lists_search");
    }
}