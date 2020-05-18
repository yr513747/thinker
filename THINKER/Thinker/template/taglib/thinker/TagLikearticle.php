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
// [ 相关文章列表 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
use app\home\logic\FieldLogic;
class TagLikearticle extends Base
{
    public $aid = '';
    public $fieldLogic;
    //初始化
    protected function init()
    {
        $this->fieldLogic = new FieldLogic();
        $this->aid = input('param.aid/d', 0);
    }
    /**
     * likearticle解析函数
     * @access    public
     * @param     array $param 查询数据条件集合
     * @param     int $row 调用行数
     * @param     string $tagid 标签id
     * @return    array
     */
    public function getLikearticle($channelid = '', $typeid = '', $limit = 12, $byabs = 0, $thumb = '')
    {
        $result = false;
        // 相关文档插件
        /*if (is_dir(root_path('weapp') . 'Likearticle')) {
            $LikearticleRow = M('Weapp')->getWeappList('Likearticle');
            if (!empty($LikearticleRow) && 1 != intval($LikearticleRow['status'])) {
                return false;
            }
        } else {
            return false;
        }*/
        $typeid = !empty($typeid) ? $typeid : '';
        $typeidArr = [];
        if (!empty($typeid)) {
            if (!preg_match('/^([\\d\\,]*)$/i', $typeid)) {
                echo '标签likearticle报错：typeid属性值语法错误，请正确填写栏目ID。';
                return false;
            }
            if (!preg_match('#,#', $typeid)) {
                $typeid = M('Arctype')->getHasChildren($typeid);
                $typeid = get_arr_column($typeid, 'id');
            }
            if (!is_array($typeid)) {
                $typeid = explode(',', $typeid);
            }
            $typeidArr = $typeid;
        }
        $keywords = [];
        //tag标签
        if (3 > count($keywords)) {
            $where_taglist = [];
            $where_taglist[] = ['aid', '=', $this->aid];
            !empty($typeidArr) && ($where_taglist[] = ['typeid', 'IN', implode(',', $typeidArr)]);
            $tag = Db::name('taglist')->field('tag')->where($where_taglist)->getArray();
            if (!empty($tag)) {
                foreach ($tag as $key => $value) {
                    $keywords[] = $value['tag'];
                }
            }
        }
        //seo关键词
        if (3 > count($keywords)) {
            $seo_keywords = Db::name('archives')->where('aid', $this->aid)->getField('seo_keywords');
            if (!empty($seo_keywords)) {
                //先根据逗号分割成数组
                $seo_key_arr = explode(',', $seo_keywords);
                foreach ($seo_key_arr as $key => $value) {
                    $keywords[] = $value;
                }
            }
        }
        $where_keyword = '';
        //如果关键词不为空,进行查询
        if (!empty($keywords)) {
            $n = 1;
            foreach ($keywords as $k) {
                if ($n > 3) {
                    break;
                }
                if (trim($k) == '') {
                    continue;
                } else {
                    $k = addslashes($k);
                }
                //关键词查询条件
                $where_keyword .= $where_keyword == '' ? " CONCAT(a.seo_keywords,' ',a.title) LIKE '%{$k}%' " : " OR CONCAT(a.seo_keywords,' ',a.title) LIKE '%{$k}%' ";
                $n++;
            }
        } else {
            return false;
        }
        //排序
        if ($byabs == 0) {
            $orderquery = " a.aid desc ";
        } else {
            $orderquery = " ABS(a.aid - " . $this->aid . ") ";
        }
        $aidArr = array();
        $field = "b.*, a.*";
        $map = [];
        if (!empty($typeidArr)) {
            $map[] = ['typeid', 'IN', implode(',', $typeidArr)];
        } else {
            if (!empty($channelid)) {
                $channelid = str_replace('，', ',', $channelid);
                $map[] = ['channel', 'IN', $channelid];
            }
        }
        $map[] = ['a.aid', '<>', $this->aid];
        $result = Db::name('archives')->field($field)->alias('a')->join('arctype b', 'b.id = a.typeid', 'LEFT')->where($map)->where($where_keyword)->orderRaw($orderquery)->limit($limit)->getArray();
        // 获取所有模型的控制器名
        $channeltypeRow = M('Channeltype')->getAll('id,ctl_name');
        $channeltypeRow = convert_arr_key($channeltypeRow, 'id');
        foreach ($result as $key => $val) {
            // 收集文档ID
            array_push($aidArr, $val['aid']);
            $controller_name = $channeltypeRow[$val['channel']]['ctl_name'];
            if ($val['is_part'] == 1) {
                $val['typeurl'] = $val['typelink'];
            } else {
                $val['typeurl'] = typeurl('home/' . $controller_name . "/lists", $val);
            }
            if ($val['is_jump'] == 1) {
                $val['arcurl'] = $val['jumplinks'];
            } else {
                $val['arcurl'] = arcurl('home/' . $controller_name . '/view', $val);
            }
            $val['litpic'] = get_default_pic($val['litpic']);
            if ('on' == $thumb) {
                $val['litpic'] = thumb_img($val['litpic']);
            }
            $result[$key] = $val;
        }
        return $result;
    }
}