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
// [ 条件筛选标签解析 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;

class TagScreening extends Base
{
    protected $tid = 0;
	
	/**
     * 获取搜索表单
     * @access public
     * @param  string  $currentstyle
	 * @param  string  $addfields
	 * @param  string  $addfieldids
	 * @param  string  $alltxt
	 * @param  string or int $typeid
     */ 
    public function getScreening($currentstyle = '', $addfields = '', $addfieldids = '', $alltxt = '', $typeid = '')
    {
        $param = input('param.');
        // 定义筛选标识
        $url_screen_var = config('global.url_screen_var');
        // 隐藏域参数处理
        $hidden = '';
       
        $this->tid = $this->getTrueTypeid($param['tid']);
        if (!empty($typeid)) {
            $this->tid = $this->getTrueTypeid($typeid);
        }
        $dirname = Db::name('arctype')->where('id', $this->tid)->getField('dirname');
        // 查询数据条件
        $where[] = array('a.is_screening', '=', 1);
        $where[] = array('a.ifeditable', '=', 1);
        $where[] = array('b.typeid', '=', $this->tid);
        // 是否指定参数读取
        if (!empty($addfields)) {
            $addfieldids = '';
            $where[] = array('a.name', 'IN', $addfields);
        } else {
            if (!empty($addfieldids)) {
                $where[] = array('a.id', 'IN', $addfieldids);
            }
        }
        // 数据查询
        $row = Db::name('channelfield')->comment('获取搜索表单');
		$row = $row->field('a.id,a.title,a.name,a.dfvalue,a.dtype');
		$row = $row->alias('a');
		$row = $row->join('channelfield_bind b', 'b.field_id = a.id', 'LEFT');
		$row = $row->where($where);
		$row = $row->select();
		$row = $row->toArray();
        // Onclick点击事件方法名称加密，防止冲突
        $OnclickScreening = 'S_' . md5('OnclickScreening');
        // Onchange改变事件方法名称加密，防止冲突
        $OnchangeScreening = 'S_' . md5('OnchangeScreening');
        // 定义搜索点击的name值
        $is_data = '';
        // 数据处理输出
        foreach ($row as $key => $value) {
            // 搜索的name值
            $name = $value['name'];
            // 封装onClick事件
            $row[$key]['onClick'] = "onClick='{$OnclickScreening}(this);'";
            // 封装onchange事件
            $row[$key]['onChange'] = "onChange='{$OnchangeScreening}(this);'";
            // 在伪静态下拼装控制器方式参数名
            if (!isset($param[$url_screen_var])) {
                $param_query = [];
                $param_query['tid'] = $dirname;
                $param_new = $param;
                unset($param_new['tid']);
                $param_query = array_merge($param_query, $param_new);
            } else {
                $param_query = $param;
            }
            /* 生成静态页面代码 */
            if (!isMobile()) {
                unset($param_query['_ajax']);
                unset($param_query['id']);
                unset($param_query['fid']);
                
            }
            /* end */
            /*筛选时，去掉url上的页码page参数*/
            unset($param_query['page']);
            /*end*/
            // 筛选值处理
            if ('region' == $value['dtype']) {
                // 类型为区域则执行
                // 处理自定义参数名称
                if (!empty($alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' == $alltxt) {
                        $alltxt = '';
                    }
                } else {
                    $alltxt = '全部';
                }
                // 拼装数组
                $all[0] = ['id' => '', 'name' => $alltxt];
                if (isset($param[$name]) && !empty($param[$name])) {
                    // 搜索点击的name值
                    $is_data = $param[$name];
                } else {
                    $is_data = $alltxt;
                }
                /*参数值含有单引号、双引号、分号，直接跳转404*/
                if (preg_match('#(\'|\\"|;)#', $is_data)) {
                    return abort(404, '页面不存在');
                }
                /*end*/
                // 处理后台添加的区域数据
                $RegionData = [];
                // 反序列化参数值
                $dfvalue = unserialize($value['dfvalue']);
                // 拆分ID值
                $region_ids = explode(',', $dfvalue['region_ids']);
                foreach ($region_ids as $id_key => $id_value) {
                    $RegionData[$id_key]['id'] = $id_value;
                }
                // 拆分name值
                $region_names = explode('，', $dfvalue['region_names']);
                foreach ($region_names as $name_key => $name_value) {
                    $RegionData[$name_key]['name'] = $name_value;
                }
                // 合并数组
                $RegionData = array_merge($all, $RegionData);
                // 处理参数输出
                foreach ($RegionData as $kk => $vv) {
                    // 参数拼装URL
                    if (!empty($vv['id'])) {
                        $param_query[$name] = $vv['id'];
                    } else {
                        unset($param_query[$name]);
                    }
                    /* 筛选标识始终追加在最后 */
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    /* end */
                    if (!empty($typeid)) {
                        // 存在typeid表示在首页展示
                        if (empty($param_query['page'])) {
                            $param_query['page'] = 1;
                        }
                    }
                    $url = url('home/Lists/index', $param_query);
                    $url = urldecode($url);
                    // 拼装onClick事件
                    $RegionData[$kk]['onClick'] = $row[$key]['onClick'] . " data-url='{$url}'";
                    // 拼装onchange参数
                    $RegionData[$kk]['SelectUrl'] = "data-url='{$url}'";
                    // 初始化参数，默认未选中
                    $RegionData[$kk]['name'] = "{$vv['name']}";
                    $RegionData[$kk]['SelectValue'] = "";
                    $RegionData[$kk]['currentstyle'] = "";
                    // 选中时执行
                    if ($vv['id'] == $is_data) {
                        $RegionData[$kk]['name'] = "<b>{$vv['name']}</b>";
                        $RegionData[$kk]['SelectValue'] = "selected";
                        $RegionData[$kk]['currentstyle'] = $currentstyle;
                    } else {
                        if ($vv['name'] == $alltxt && $is_data == $alltxt) {
                            $RegionData[$kk]['name'] = "<b>{$vv['name']}</b>";
                            $RegionData[$kk]['SelectValue'] = "selected";
                            $RegionData[$kk]['currentstyle'] = $currentstyle;
                        }
                    }
                }
                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $RegionData;
            } else {
                // 类型不为区域则执行
                $dfvalue = explode(',', $value['dfvalue']);
                $all[0] = '全部';
                if (!empty($alltxt)) {
                    // 等于OFF表示关闭，不需要此项
                    if ('off' == $alltxt) {
                        $all[0] = '';
                    } else {
                        $all[0] = $alltxt;
                    }
                }
                if (isset($param[$name]) && !empty($param[$name])) {
                    // 搜索点击的name值
                    $is_data = $param[$name];
                } else {
                    $is_data = $alltxt;
                }
                /*参数值含有单引号、双引号、分号，直接跳转404*/
                if (preg_match('#(\'|\\"|;)#', $is_data)) {
                    return abort(404, '页面不存在');
                }
                /*end*/
                // 合并数组
                $dfvalue = array_merge($all, $dfvalue);
                // 处理参数输出
                $data_new = [];
                foreach ($dfvalue as $kk => $vv) {
                    if ('off' == $alltxt && empty($vv)) {
                        continue;
                    }
                    $param_query[$name] = $vv;
                    $data_new[$kk]['id'] = $vv;
                    $data_new[$kk]['name'] = "{$vv}";
                    $data_new[$kk]['SelectValue'] = "";
                    $data_new[$kk]['currentstyle'] = "";
                    // 目前单选类型选中和多选类型选中的数据处理是相同的，后续可能会有优化，暂时保留两个判断
                    if ($vv == $is_data) {
                        // 单选/下拉类型选中
                        $data_new[$kk]['name'] = "<b>{$vv}</b>";
                        $data_new[$kk]['SelectValue'] = "selected";
                        $data_new[$kk]['currentstyle'] = $currentstyle;
                    } else {
                        if ($vv . '|' == $is_data) {
                            // 多选类型选中
                            $data_new[$kk]['name'] = "<b>{$vv}</b>";
                            $data_new[$kk]['SelectValue'] = "selected";
                            $data_new[$kk]['currentstyle'] = $currentstyle;
                        } else {
                            if ($vv == $all[0] && empty($is_data)) {
                                // “全部” 按钮选中
                                $data_new[$kk]['name'] = "<b>{$vv}</b>";
                                $data_new[$kk]['SelectValue'] = "selected";
                                $data_new[$kk]['currentstyle'] = $currentstyle;
                            }
                        }
                    }
                    if ($all[0] == $vv) {
                        // 若选中 “全部” 按钮则清除这个字段参数
                        unset($param_query[$name]);
                    } else {
                        if ('checkbox' == $value['dtype']) {
                            // 等于多选类型，则拼装上-号，用于搜索时分割，可匹配数据
                            $param_query[$name] = $vv . '|';
                        }
                    }
                    /* 筛选标识始终追加在最后 */
                    unset($param_query[$url_screen_var]);
                    $param_query[$url_screen_var] = 1;
                    /* end */
                    // 参数拼装URL
                    if (!empty($typeid)) {
                        // 存在typeid表示在首页展示
                        if (empty($param_query['page'])) {
                            $param_query['page'] = 1;
                        }
                    }
                    $url = url('home/Lists/index', $param_query);
                    $url = urldecode($url);
                    // 封装onClick
                    $data_new[$kk]['onClick'] = $row[$key]['onClick'] . " data-url='{$url}'";
                    // 封装onchange事件
                    $data_new[$kk]['SelectUrl'] = "data-url='{$url}'";
                }
                // 数据赋值到数组中
                $row[$key]['dfvalue'] = $data_new;
            }
        }
        $resetUrl = url('home/Lists/index', array('tid' => $dirname, $url_screen_var => 1));
        $hidden .= <<<EOF
<script type="text/javascript">
    function {$OnclickScreening}(obj) {
        var dataurl = \$(obj).attr('data-url');
        if (dataurl) {
            window.location.href = dataurl;
        }else{
            layer.msg(res.msg, {time: 2000, icon: 2});
        }
    }

    function {$OnchangeScreening}(obj) {
        var dataurl = \$(obj).find("option:selected").attr('data-url');
        if (dataurl) {
            window.location.href = dataurl;
        }else{
            layer.msg(res.msg, {time: 2000, icon: 2});
        }
    }
</script>
EOF;
        $result = array('hidden' => $hidden, 'resetUrl' => $resetUrl, 'list' => $row);
        return $result;
    }
}