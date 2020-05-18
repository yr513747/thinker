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
// [ 获取单个广告信息 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagAd extends Base
{
    public function getAd($aid = '')
    {
        if (empty($aid)) {
            echo '标签ad报错：缺少属性 aid 值。';
            return false;
        }
        $result = Db::name("ad")->where(['id' => $aid])->cache(true, CACHE_TIME, "ad")->getOne();
        if (empty($result)) {
            echo '标签ad报错：该广告ID(' . $aid . ')不存在。';
            return false;
        }
        // 默认无图封面
        $result['litpic'] = handle_subdir(get_default_pic($result['litpic']));
        // 解码内容
        $result['intro'] = htmlspecialchars_decode($result['intro']);
        $result['target'] = $result['target'] == 1 ? 'target="_blank"' : 'target="_self"';
        // 支持子目录
        $result['intro'] = handle_subdir($result['intro'], 'html');
        return $result;
    }
}