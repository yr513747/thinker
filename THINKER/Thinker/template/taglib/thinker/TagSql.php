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
// [ SQL万能标签 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
class TagSql extends Base
{
    public function getSql($sql = '', $cachetime = '')
    {
        if (empty($sql)) {
            echo '标签sql报错：缺少属性 sql 值。';
            return false;
        }
        if ($cachetime === '') {
            $cachetime = CACHE_TIME;
        }
        $sql = str_replace(' eq ', ' = ', $sql);
        // 等于
        $sql = str_replace(' neq  ', ' != ', $sql);
        // 不等于
        $sql = str_replace(' gt ', ' > ', $sql);
        // 大于
        $sql = str_replace(' egt ', ' >= ', $sql);
        // 大于等于
        $sql = str_replace(' lt ', ' < ', $sql);
        // 小于
        $sql = str_replace(' elt ', ' <= ', $sql);
        // 小于等于
        $sql = str_replace('__PREFIX__', $this->params['prefix'], $sql);
        // 替换前缀
        $cacheKey = "tagSql_" . md5($sql);
        $result = cache($cacheKey);
        if (empty($result)) {
            $result = Db::query($sql);
            cache($cacheKey, $result, $cachetime);
        }
        return $result;
    }
}