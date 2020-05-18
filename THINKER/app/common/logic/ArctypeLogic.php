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
// [ 栏目逻辑定义 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\common\logic;

use Thinker\basic\BaseLogic;
use think\facade\Db;
class ArctypeLogic extends BaseLogic
{
    /**
     * 获得指定栏目下的子栏目的数组
     *
     * @access  public
     * @param   int     $id     栏目的ID
     * @param   int     $selected   当前选中栏目的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level      限定返回的级数。为0时返回所有级数
     * @param   array   $map      查询条件
     * @return  mix
     */
    public function arctypeList($id = 0, $selected = 0, $re_type = true, $level = 0, $map = array(), $is_cache = true)
    {
        static $res = NULL;
        if ($res === NULL) {
            $where = [];
			$newwhere = [];
            $where[] = ['status', '=', 1];
            // 权限控制
            $admin_info = session('admin_info');
            if (in_array($this->request->app(), ['admin']) && 0 < intval($admin_info['role_id'])) {
                $auth_role_info = $admin_info['auth_role_info'];
                if (!empty($auth_role_info)) {
                    if (!empty($auth_role_info['permission']['arctype'])) {
                        $where[] = ['id', 'IN', $auth_role_info['permission']['arctype']];
                    }
                }
            }
            if (!empty($map)) {
                $where = array_merge($where, $map);
            }
            foreach ($where as $key => $val) {
				if (!isset($val[0]) || !isset($val[1]) || !isset($val[2])) {
                    continue;
                }
                $newval = ['c.' . $val[0], $val[1], $val[2]];
                $newwhere[$key] = $newval;
            }
            $fields = "c.*, c.id as typeid, count(s.id) as has_children, '' as children";
            $res = Db::name('arctype');
            $res = $res->comment('app\\common\\logic\\ArctypeLogic->arctypeList');
            $res = $res->field($fields);
            $res = $res->alias('c');
            $res = $res->join('arctype s', 's.parent_id = c.id', 'LEFT');
            $res = $res->where($newwhere);
            $res = $res->group('c.id');
            $res = $res->order('c.parent_id asc, c.sort_order asc, c.id');
            $res = $res->cache($is_cache, CACHE_TIME, "arctype");
            $res = $res->select();
            $res = $res->toArray();
        }
        if (empty($res)) {
            return $re_type ? '' : array();
        }
        $options = $this->arctypeOptions($id, $res);
        // 获得指定栏目下的子栏目的数组
        // 截取到指定的缩减级别 
        if ($level > 0) {
            if ($id == 0) {
                $end_level = $level;
            } else {
                $first_item = reset($options);
                // 获取第一个元素
                $end_level = $first_item['level'] + $level;
            }
            // 保留level小于end_level的部分 
            foreach ($options as $key => $val) {
                if ($val['level'] >= $end_level) {
                    unset($options[$key]);
                }
            }
        }
        $pre_key = 0;
        foreach ($options as $key => $value) {
            $options[$key]['has_children'] = 0;
            if ($pre_key > 0) {
                if ($options[$pre_key]['id'] == $options[$key]['parent_id']) {
                    $options[$pre_key]['has_children'] = 1;
                }
            }
            $pre_key = $key;
        }
        if ($re_type == true) {
            $select = '';
            foreach ($options as $var) {
                $select .= '<option value="' . $var['id'] . '" ';
                $select .= $selected == $var['id'] ? "selected='true'" : '';
                $select .= '>';
                if ($var['level'] > 0) {
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars_decode(addslashes($var['typename'])) . '</option>';
            }
            return $select;
        } else {
            return $options;
        }
    }
    /**
     * 过滤和排序所有文章栏目，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $id     上级栏目ID
     * @param   array   $arr        含有所有栏目的数组
     * @param   int     $level      级别
     * @return  void
     */
    public function arctypeOptions($spec_id, $arr)
    {
        static $cat_options = array();
        if (isset($cat_options[$spec_id])) {
            return $cat_options[$spec_id];
        }
        if (!isset($cat_options[0])) {
            $level = $last_id = 0;
            $options = $id_array = $level_array = array();
            while (!empty($arr)) {
                foreach ($arr as $key => $value) {
                    $id = $value['id'];
                    if ($level == 0 && $last_id == 0) {
                        if ($value['parent_id'] > 0) {
                            break;
                        }
                        $options[$id] = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id'] = $id;
                        $options[$id]['typename'] = htmlspecialchars_decode($value['typename']);
                        unset($arr[$key]);
                        if ($value['has_children'] == 0) {
                            continue;
                        }
                        $last_id = $id;
                        $id_array = array($id);
                        $level_array[$last_id] = ++$level;
                        continue;
                    }
                    if ($value['parent_id'] == $last_id) {
                        $options[$id] = $value;
                        $options[$id]['level'] = $level;
                        $options[$id]['id'] = $id;
                        $options[$id]['typename'] = htmlspecialchars_decode($value['typename']);
                        unset($arr[$key]);
                        if ($value['has_children'] > 0) {
                            if (end($id_array) != $last_id) {
                                $id_array[] = $last_id;
                            }
                            $last_id = $id;
                            $id_array[] = $id;
                            $level_array[$last_id] = ++$level;
                        }
                    } elseif ($value['parent_id'] > $last_id) {
                        break;
                    }
                }
                $count = count($id_array);
                if ($count > 1) {
                    $last_id = array_pop($id_array);
                } elseif ($count == 1) {
                    if ($last_id != end($id_array)) {
                        $last_id = end($id_array);
                    } else {
                        $level = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }
                if ($last_id && isset($level_array[$last_id])) {
                    $level = $level_array[$last_id];
                } else {
                    $level = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        } else {
            $options = $cat_options[0];
        }
        if (!$spec_id) {
            return $options;
        } else {
            if (empty($options[$spec_id])) {
                return array();
            }
            $spec_id_level = $options[$spec_id]['level'];
            foreach ($options as $key => $value) {
                if ($key != $spec_id) {
                    unset($options[$key]);
                } else {
                    break;
                }
            }
            $spec_id_array = array();
            foreach ($options as $key => $value) {
                if ($spec_id_level == $value['level'] && $value['id'] != $spec_id || $spec_id_level > $value['level']) {
                    break;
                } else {
                    $spec_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_id] = $spec_id_array;
            return $spec_id_array;
        }
    }
    /**
     * 获取栏目的目录名称，确保唯一性
     */
    public function getDirname($typename = '', $dirname = '', $id = 0, $newDirnameArr = [])
    {
        $id = intval($id);
        if (!trim($dirname) || empty($dirname)) {
            $dirname = get_pinyin($typename);
        }
        if (strval(intval($dirname)) == strval($dirname)) {
            $dirname .= get_rand_str(3, 0, 2);
        }
        $dirname = preg_replace('/(\\s)+/', '_', $dirname);
        if (!$this->dirnameUnique($dirname, $id, $newDirnameArr)) {
            $nowDirname = $dirname . get_rand_str(3, 0, 2);
            return $this->getDirname($typename, $nowDirname, $id, $newDirnameArr);
        }
        return $dirname;
    }
    /**
     * 判断目录名称的唯一性
     */
    public function dirnameUnique($dirname = '', $typeid = 0, $newDirnameArr = [])
    {
        $result = Db::name('arctype')->comment('app\\common\\logic\\ArctypeLogic->dirnameUnique')->field('id,dirname')->getAllWithIndex('id');
        if (!empty($result)) {
            if (0 < $typeid) {
                unset($result[$typeid]);
            }
            !empty($result) && ($result = get_arr_column($result, 'dirname'));
        }
        empty($result) && ($result = []);
        $disableDirname = config('global.disable_dirname');
        $disableDirname = array_merge($disableDirname, $result);
        !empty($newDirnameArr) && ($disableDirname = array_merge($disableDirname, $newDirnameArr));
        if (in_array(strtolower($dirname), $disableDirname)) {
            return false;
        }
        return true;
    }
}