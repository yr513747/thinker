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
// [ 公共函数文件 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
// --------------------------------------------------------------------------

include_once __DIR__."/function.php";

if (!function_exists('AddOrderAction')) 
{
    /**
     * 添加订单操作表数据
     * @param string,array  $OrderId       订单ID或订单ID数组
     * @param string        $UsersId       会员ID，若不为0，则ActionUsers为0
     * @param int           $ActionUsers   操作员ID，为0，表示会员操作，反之则为管理员ID
     * @param int           $OrderStatus   操作时，订单当前状态
     * @param int           $ExpressStatus 操作时，订单当前物流状态
     * @param int           $PayStatus     操作时，订单当前付款状态
     * @param string        $ActionDesc    操作描述
     * @param string        $ActionNote    操作备注
     * return void
     */
    function AddOrderAction($OrderId,$UsersId,$ActionUsers='0',$OrderStatus='0',$ExpressStatus='0',$PayStatus='0',$ActionDesc='提交订单！',$ActionNote='会员提交订单成功！')
    {
        if (is_array($OrderId) && '4' == $OrderStatus) {
            // 订单状态为过期
            foreach ($OrderId as $key => $value) {
                $ActionData[] = [
                    'order_id'       => $value['order_id'],
                    'users_id'       => $UsersId,
                    'action_user'    => $ActionUsers,
                    'order_status'   => $OrderStatus,
                    'express_status' => $ExpressStatus,
                    'pay_status'     => $PayStatus,
                    'action_desc'    => $ActionDesc,
                    'action_note'    => $ActionNote,
                    'add_time'       => getTime(),
                ];
            }
            // 批量添加
            \think\facade\Db::name('shop_order_log')->insertAll($ActionData);
        }else{
            $ActionData = [
                'order_id'       => $OrderId,
                'users_id'       => $UsersId,
                'action_user'    => $ActionUsers,
                'order_status'   => $OrderStatus,
                'express_status' => $ExpressStatus,
                'pay_status'     => $PayStatus,
                'action_desc'    => $ActionDesc,
                'action_note'    => $ActionNote,
                'add_time'       => getTime(),
            ];
            // 单条添加
            \think\facade\Db::name('shop_order_log')->add($ActionData);
        }
    }
}
if (!function_exists('GetUsersLatestData')) 
{
    /**
     * 获取登录的会员最新数据
     */
    function GetUsersLatestData($users_id = null) {
        $users_id = empty($users_id) ? session('users_id') : $users_id;
        if(!empty($users_id)) {
            /*读取的字段*/
            $field = 'a.*, b.*, b.discount as level_discount';
            /* END */

            /*查询数据*/
            $users = \think\facade\Db::name('users')->field($field)
                ->alias('a')
                ->join('users_level b', 'a.level = b.level_id', 'LEFT')
                ->where([
                    'a.users_id'        => $users_id,
                    
                    'a.is_activation'   => 1,
                    'a.is_del'          => 0,
                ])->find();
            // 会员不存在则返回空
            if (empty($users)) return false;
            /* END */

            /*会员数据处理*/
            // 头像处理
            $users['head_pic'] = get_head_pic($users['head_pic']);
            // 昵称处理
            $users['nickname'] = empty($users['nickname']) ? $users['username'] : $users['nickname'];
            // 密码为空并且存在openid则表示微信注册登录，密码字段更新为0，可重置密码一次。
            $users['password'] = empty($users['password']) && !empty($users['thirdparty']) ? 1 : 1;
            // 删除登录密码及支付密码
            unset($users['paypwd']);
            // 级别处理
            $LevelData = [];
            if (intval($users['level_maturity_days']) >= 36600) {
                $users['maturity_code'] = 1;
                $users['maturity_date'] = '终身';
            }else if (0 == $users['open_level_time'] && 0 == $users['level_maturity_days']) {
                $users['maturity_code'] = 0;
                $users['maturity_date'] = '未升级会员';// 没有升级会员，置空
            }else{
                /*计算剩余天数*/
                $days = $users['open_level_time'] + ($users['level_maturity_days'] * 86400);
                // 取整
                $days = ceil(($days - getTime()) / 86400);
                if (0 >= $days) {
                    /*更新会员的级别*/
                    $LevelData = M('Users')->UpUsersLevelData($users_id);
                    /* END */
                    $users['maturity_code'] = 2;
                    $users['maturity_date'] = '未升级会员';// 会员过期，置空
                }else{
                    $users['maturity_code'] = 3;
                    $users['maturity_date'] = $days.' 天';
                }
                /* END */
            }
            /* END */
            
            // 合并数据
            $LatestData = array_merge($users, $LevelData);
            /*更新session*/
            session('users', $LatestData);
            session('users_id', $LatestData['users_id']);
            cookie('users_id', $LatestData['users_id']);
            /* END */
            // 返回数据
            return $LatestData;
        }else{
            // session中不存在会员ID则返回空
            return false;
        }
    }
}
if (!function_exists('getDirFile')) 
{ 
    /**
     * 递归读取文件夹文件
     *
     * @param string $directory 目录路径
     * @param string $dir_name 显示的目录前缀路径
     * @param array $arr_file 是否删除空目录
     * @return boolean
     */
    function getDirFile($directory, $dir_name='', &$arr_file = array()) {
        if (!file_exists($directory) ) {
            return false;
        }

        $mydir = dir($directory);
        while($file = $mydir->read())
        {
            if((is_dir("$directory/$file")) AND ($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    getDirFile("$directory/$file", "$dir_name/$file", $arr_file);
                } else {
                    getDirFile("$directory/$file", "$file", $arr_file);
                }
                
            }
            else if(($file != ".") AND ($file != ".."))
            {
                if ($dir_name) {
                    $arr_file[] = "$dir_name/$file";
                } else {
                    $arr_file[] = "$file";
                }
            }
        }
        $mydir->close();

        return $arr_file;
    }
}

if (!function_exists('is_realdomain')) 
{
    /**
     * 简单判断当前访问的域名是否真实
     * @param string $domain 不带协议的域名
     * @return boolean
     */
    function is_realdomain($domain = '')
    {
        $is_real = false;
        $domain = !empty($domain) ? $domain : \think\facade\Request::host();
        if (!preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i', $domain) && 'localhost' != $domain && '127.0.0.1' != \think\facade\Request::serverIP()) {
            $is_real = true;
        }

        return $is_real;
    }
}
if (!function_exists('extra_cache')) {
/**
 * 获取和设置配置参数 支持批量定义
 * @param string|array $name 配置变量
 * @param mixed $value 配置值
 * @param mixed $default 默认值
 * @return mixed
 */
    function extra_cache($name, $value = '', $expire = 0) {
       
        $module = strtolower(\think\facade\Request::app());
        $keys_list = config('extra_cache_key');

        $key = md5(strtolower($name));
        if (!isset($keys_list[$name])) {
            return false;
        }
        $options = $keys_list[$name]['options'];
        $cache_conf = config('cache');
        if ($expire > 0) {
            $cache_conf['expire'] = $expire;
        } else {
            if (!empty($options['expire'])) {
                $cache_conf['expire'] = $options['expire'];
            }
        }
        if (!empty($options['prefix'])) {
            $cache_conf['prefix'] = $options['prefix'];
        }

        $tag = $keys_list[$name]['tag'];
        if (empty($tag)) {
            $tag = $module;
        }

        return cache($key, $value, $cache_conf, $tag);
   }   
}
if (!function_exists('filter_line_return')) 
{
    /**
     *  过滤换行回车符
     *
     * @param     string  $str     字符串信息
     * @return    string
     */
    function filter_line_return($str = '', $replace = '')
    {
        return str_replace(PHP_EOL, $replace, $str);
    }
}

if (!function_exists('format_bytes')) 
{
    /**
     * 格式化字节大小
     *
     * @param  number $size      字节数
     * @param  string $delimiter 数字和单位分隔符
     * @return string            格式化后的带单位的大小
     */
    function format_bytes($size, $delimiter = '') {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
        return round($size, 2) . $delimiter . $units[$i];
    }
}

if (!function_exists('unformat_bytes')) 
{
    /**
     * 反格式化字节大小
     *
     * @param  number $size      格式化带单位的大小
     */
    function unformat_bytes($formatSize)
    {
        $size = 0;
        if (preg_match('/^\d+P/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+T/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+G/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024 * 1024;
        } else if (preg_match('/^\d+M/i', $formatSize)) {
            $size = intval($formatSize) * 1024 * 1024;
        } else if (preg_match('/^\d+K/i', $formatSize)) {
            $size = intval($formatSize) * 1024;
        } else if (preg_match('/^\d+B/i', $formatSize)) {
            $size = intval($formatSize);
        }

        $size = strval($size);

        return $size;
    }
}

if (!function_exists('delFile')) 
{  
    /**
     * 递归删除文件夹
     *
     * @param string $path 目录路径
     * @param boolean $delDir 是否删除空目录
     * @return boolean
     */
    function delFile($path, $delDir = FALSE) {
        if(!is_dir($path))
            return FALSE;       
        $handle = @opendir($path);
        if ($handle) {
            while (false !== ( $item = readdir($handle) )) {
                if ($item != "." && $item != "..")
                    is_dir("$path/$item") ? delFile("$path/$item", $delDir) : @unlink("$path/$item");
            }
            closedir($handle);
            if ($delDir) {
                return @rmdir($path);
            }
        }else {
            if (file_exists($path)) {
                return @unlink($path);
            } else {
                return FALSE;
            }
        }
    }
}
if (!function_exists('allow_release_arctype')) 
{
    /**
     * 允许发布文档的栏目列表
     */
    function allow_release_arctype($selected = 0, $allow_release_channel = array(), $selectform = true)
    {
        $where = [];

        //$where[] = ['c.weapp_code','=',null]; // 回收站功能
       
        $where[] = ['c.is_del','=',0]; // 回收站功能

        /*权限控制*/
        $admin_info = session('admin_info');
        if (0 < intval($admin_info['role_id'])) {
            $auth_role_info = $admin_info['auth_role_info'];
            if(! empty($auth_role_info)){
                if(! empty($auth_role_info['permission']['arctype'])){
                    $where[] = array('c.id','IN', $auth_role_info['permission']['arctype']);
                }
            }
        }
        /*--end*/

        if (!is_array($selected)) {
            $selected = [$selected];
        }

        $cacheKey = json_encode($selected).json_encode($allow_release_channel).$selectform.json_encode($where);
        $select_html = cache($cacheKey);
        if (empty($select_html) || false == $selectform) {
            /*允许发布文档的模型*/
            $allow_release_channel = !empty($allow_release_channel) ? $allow_release_channel : config('global.allow_release_channel');

            /*所有栏目分类*/
            $arctype_max_level = intval(config('global.arctype_max_level'));
           
			$where[] = ['c.status','=',1];
            $fields = "c.id, c.parent_id, c.current_channel, c.typename, c.grade, count(s.id) as has_children, '' as children";
            $res = \think\facade\Db::name('arctype')
                ->field($fields)
                ->alias('c')
                ->join('arctype s','s.parent_id = c.id','LEFT')
                ->where($where)
                ->group('c.id')
                ->order('c.parent_id asc, c.sort_order asc, c.id')
                ->cache(true,CACHE_TIME,"arctype")
                ->select()->toArray();
            /*--end*/
            if (empty($res)) {
                return '';
            }

            /*过滤掉第三级栏目属于不允许发布的模型下*/
            foreach ($res as $key => $val) {
                if ($val['grade'] == ($arctype_max_level - 1) && !in_array($val['current_channel'], $allow_release_channel)) {
                    unset($res[$key]);
                }
            }
            /*--end*/

            /*所有栏目列表进行层次归类*/
            $arr = group_same_key($res, 'parent_id');
            for ($i=0; $i < $arctype_max_level; $i++) {
                foreach ($arr as $key => $val) {
                    foreach ($arr[$key] as $key2 => $val2) {
                        if (!isset($arr[$val2['id']])) {
                            $arr[$key][$key2]['has_children'] = 0;
                            continue;
                        }
                        $val2['children'] = $arr[$val2['id']];
                        $arr[$key][$key2] = $val2;
                    }
                }
            }
            /*--end*/

            /*过滤掉第二级不包含允许发布模型的栏目*/
            $nowArr = $arr[0];
            foreach ($nowArr as $key => $val) {
                if (!empty($nowArr[$key]['children'])) {
                    foreach ($nowArr[$key]['children'] as $key2 => $val2) {
                        if (empty($val2['children']) && !in_array($val2['current_channel'], $allow_release_channel)) {
                            unset($nowArr[$key]['children'][$key2]);
                        }
                    }
                }
                if (empty($nowArr[$key]['children']) && !in_array($nowArr[$key]['current_channel'], $allow_release_channel)) {
                    unset($nowArr[$key]);
                    continue;
                }
            }
            /*--end*/

            /*组装成层级下拉列表框*/
            $select_html = '';
            if (false == $selectform) {
                $select_html = $nowArr;
            } else if (true == $selectform) {
                foreach ($nowArr AS $key => $val)
                {
                    $select_html .= '<option value="' . $val['id'] . '" data-grade="' . $val['grade'] . '" data-current_channel="' . $val['current_channel'] . '"';
                    $select_html .= (in_array($val['id'], $selected)) ? ' selected="ture"' : '';
                    if (!empty($allow_release_channel) && !in_array($val['current_channel'], $allow_release_channel)) {
                        $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                    }
                    $select_html .= '>';
                    if ($val['grade'] > 0)
                    {
                        $select_html .= str_repeat('&nbsp;', $val['grade'] * 4);
                    }
                    $select_html .= htmlspecialchars_decode(addslashes($val['typename'])) . '</option>';

                    if (empty($val['children'])) {
                        continue;
                    }
                    foreach ($nowArr[$key]['children'] as $key2 => $val2) {
                        $select_html .= '<option value="' . $val2['id'] . '" data-grade="' . $val2['grade'] . '" data-current_channel="' . $val2['current_channel'] . '"';
                        $select_html .= (in_array($val2['id'], $selected)) ? ' selected="ture"' : '';
                        if (!empty($allow_release_channel) && !in_array($val2['current_channel'], $allow_release_channel)) {
                            $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                        }
                        $select_html .= '>';
                        if ($val2['grade'] > 0)
                        {
                            $select_html .= str_repeat('&nbsp;', $val2['grade'] * 4);
                        }
                        $select_html .= htmlspecialchars_decode(addslashes($val2['typename'])) . '</option>';

                        if (empty($val2['children'])) {
                            continue;
                        }
                        foreach ($nowArr[$key]['children'][$key2]['children'] as $key3 => $val3) {
                            $select_html .= '<option value="' . $val3['id'] . '" data-grade="' . $val3['grade'] . '" data-current_channel="' . $val3['current_channel'] . '"';
                            $select_html .= (in_array($val3['id'], $selected)) ? ' selected="ture"' : '';
                            if (!empty($allow_release_channel) && !in_array($val3['current_channel'], $allow_release_channel)) {
                                $select_html .= ' disabled="true" style="background-color:#f5f5f5;"';
                            }
                            $select_html .= '>';
                            if ($val3['grade'] > 0)
                            {
                                $select_html .= str_repeat('&nbsp;', $val3['grade'] * 4);
                            }
                            $select_html .= htmlspecialchars_decode(addslashes($val3['typename'])) . '</option>';
                        }
                    }
                }

                cache($cacheKey, $select_html, null, 'admin_archives_release');
                
            }
        }

        return $select_html;
    }
}
if (!function_exists('view_logic'))
{
    /**
     * 模型对应逻辑
     * @param intval $aid 文档ID
     * @param intval $channel 栏目ID
     * @param intval $result 数组
     * @param mix $allAttrInfo 附加表数据
     * @return array
     */
    function view_logic($aid, $channel, $result = array(), $allAttrInfo = array())
    {
        $allAttrInfo_bool = $allAttrInfo;
        $result['image_list'] = $result['attr_list'] = $result['file_list'] = array();
        switch ($channel) {
            case '2': // 产品模型
            {
                /*产品相册*/
                if (true === $allAttrInfo_bool) {
                    $allAttrInfo = [];
                    $allAttrInfo['product_img'] = M('ProductImg')->getProImg([$aid]);
                }
                $image_list = !empty($allAttrInfo['product_img'][$aid]) ? $allAttrInfo['product_img'][$aid] : [] ;

                // 支持子目录
                foreach ($image_list as $k1 => $v1) {
                    $image_list[$k1]['image_url'] = handle_subdir($v1['image_url']);
                }

                $result['image_list'] = $image_list;
                /*--end*/

                /*产品参数*/
                if (true === $allAttrInfo_bool) {
                    $allAttrInfo = [];
                    $allAttrInfo['product_attr'] = M('ProductAttr')->getProAttr([$aid]);
                }
                $attr_list = !empty($allAttrInfo['product_attr'][$aid]) ? $allAttrInfo['product_attr'][$aid] : [] ;
                
                $result['attr_list'] = $attr_list;
                /*--end*/
                break;
            }

            case '3': // 图集模型
            {
                /*图集相册*/
                if (true === $allAttrInfo_bool) {
                    $allAttrInfo = [];
                    $allAttrInfo['images_upload'] = M('ImagesUpload')->getImgUpload([$aid]);
                }
                $image_list = !empty($allAttrInfo['images_upload'][$aid]) ? $allAttrInfo['images_upload'][$aid] : [] ;

                // 支持子目录
                foreach ($image_list as $k1 => $v1) {
                    $image_list[$k1]['image_url'] = handle_subdir($v1['image_url']);
                }

                $result['image_list'] = $image_list;
                /*--end*/
                break;
            }

            case '4': // 下载模型
            {
                /*下载资料列表*/
                if (true === $allAttrInfo_bool) {
                    $allAttrInfo = [];
                    $allAttrInfo['download_file'] = M('DownloadFile')->getDownFile([$aid]);
                }
                $file_list = !empty($allAttrInfo['download_file'][$aid]) ? $allAttrInfo['download_file'][$aid] : [] ;

                // 支持子目录
                foreach ($file_list as $k1 => $v1) {
                    $file_list[$k1]['file_url'] = handle_subdir($v1['file_url']);
                }

                $result['file_list'] = $file_list;
                /*--end*/
                break;
            }

            default:
            {
                break;
            }
        }

        return $result;
    }
}
if (!function_exists('get_controller_byct')) {
    /**
     * 根据模型ID获取控制器的名称
     * @return mixed
     */
    function get_controller_byct($current_channel)
    {
        $channeltype_info = M('Channeltype')->getInfo($current_channel);
        return $channeltype_info['ctl_name'];
    }
}

if (!function_exists('gettoptype')) 
{
    /**
     * 获取当前栏目的第一级栏目
     */
    function gettoptype($typeid, $field = 'typename')
    {
        $parent_list = M('Arctype')->getAllPid($typeid); // 获取当前栏目的所有父级栏目
        $result = current($parent_list); // 第一级栏目
        if (isset($result[$field]) && !empty($result[$field])) {
            return handle_subdir($result[$field]); // 支持子目录
        } else {
            return '';
        }
    }
}
if (!function_exists('img_style_wh')) 
{
    /**
     * 追加指定内嵌样式到编辑器内容的img标签，兼容图片自动适应页面
     */
    function img_style_wh($content = '', $title = '')
    {
        if (!empty($content)) {
            preg_match_all('/<img.*(\/)?>/iUs', $content, $imginfo);
            $imginfo = !empty($imginfo[0]) ? $imginfo[0] : [];
            if (!empty($imginfo)) {
                $num = 1;
                $appendStyle = "max-width:100%!important;height:auto!important;";
                $title = preg_replace('/("|\')/i', '', $title);
                foreach ($imginfo as $key => $imgstr) {
                    $imgstrNew = $imgstr;
                    
                    /* 兼容已存在的多重追加样式，处理去重 */
                    if (stristr($imgstrNew, $appendStyle.$appendStyle)) {
                        $imgstrNew = preg_replace('/'.$appendStyle.$appendStyle.'/i', '', $imgstrNew);
                    }
                    if (stristr($imgstrNew, $appendStyle)) {
                        $content = str_ireplace($imgstr, $imgstrNew, $content);
                        $num++;
                        continue;
                    }
                    /* end */

                    // 追加style属性
                    $imgstrNew = preg_replace('/style(\s*)=(\s*)[\'|\"](.*?)[\'|\"]/i', 'style="'.$appendStyle.'${3}"', $imgstrNew);
                    if (!preg_match('/<img(.*?)style(\s*)=(\s*)[\'|\"](.*?)[\'|\"](.*?)[\/]?(\s*)>/i', $imgstrNew)) {
                        // 新增style属性
                        $imgstrNew = str_ireplace('<img', "<img style=\"".$appendStyle."\" ", $imgstrNew);
                    }

                    // 移除img中多余的title属性
                    // $imgstrNew = preg_replace('/title(\s*)=(\s*)[\'|\"]([\w\.]*?)[\'|\"]/i', '', $imgstrNew);

                    // 追加alt属性
                    $altNew = $title."(图{$num})";
                    $imgstrNew = preg_replace('/alt(\s*)=(\s*)[\'|\"]([\w\.]*?)[\'|\"]/i', 'alt="'.$altNew.'"', $imgstrNew);
                    if (!preg_match('/<img(.*?)alt(\s*)=(\s*)[\'|\"](.*?)[\'|\"](.*?)[\/]?(\s*)>/i', $imgstrNew)) {
                        // 新增alt属性
                        $imgstrNew = str_ireplace('<img', "<img alt=\"{$altNew}\" ", $imgstrNew);
                    }

                    // 追加title属性
                    $titleNew = $title."(图{$num})";
                    $imgstrNew = preg_replace('/title(\s*)=(\s*)[\'|\"]([\w\.]*?)[\'|\"]/i', 'title="'.$titleNew.'"', $imgstrNew);
                    if (!preg_match('/<img(.*?)title(\s*)=(\s*)[\'|\"](.*?)[\'|\"](.*?)[\/]?(\s*)>/i', $imgstrNew)) {
                        // 新增alt属性
                        $imgstrNew = str_ireplace('<img', "<img alt=\"{$titleNew}\" ", $imgstrNew);
                    }
                    
                    // 新的img替换旧的img
                    $content = str_ireplace($imgstr, $imgstrNew, $content);
                    $num++;
                }
            }
        }

        return $content;
    }
}
if (!function_exists('arctype_options')) 
{
    /**
     * 过滤和排序所有文章栏目，返回一个带有缩进级别的数组
     * @param   int     $id     上级栏目ID
     * @param   array   $arr        含有所有栏目的数组
     * @param   string     $id_alias      id键名
     * @param   string     $pid_alias      父id键名
     * @return  array
     */
    function arctype_options($spec_id, $arr, $id_alias, $pid_alias)
    {
        $cat_options = array();

        if (isset($cat_options[$spec_id]))
        {
            return $cat_options[$spec_id];
        }

        if (!isset($cat_options[0]))
        {
            $level = $last_id = 0;
            $options = $id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $id = $value[$id_alias];
                    if ($level == 0 && $last_id == 0)
                    {
                        if ($value[$pid_alias] > 0)
                        {
                            break;
                        }

                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id][$id_alias]    = $id;
                        // $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);

                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_id  = $id;
                        $id_array = array($id);
                        $level_array[$last_id] = ++$level;
                        continue;
                    }

                    if ($value[$pid_alias] == $last_id)
                    {
                        $options[$id]          = $value;
                        $options[$id]['level'] = $level;
                        $options[$id][$id_alias]    = $id;
                        // $options[$id]['typename']  = $value['typename'];
                        unset($arr[$key]);

                        if ($value['has_children'] > 0)
                        {
                            if (end($id_array) != $last_id)
                            {
                                $id_array[] = $last_id;
                            }
                            $last_id    = $id;
                            $id_array[] = $id;
                            $level_array[$last_id] = ++$level;
                        }
                    }
                    elseif ($value[$pid_alias] > $last_id)
                    {
                        break;
                    }
                }

                $count = count($id_array);
                if ($count > 1)
                {
                    $last_id = array_pop($id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_id != end($id_array))
                    {
                        $last_id = end($id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_id = 0;
                        $id_array = array();
                        continue;
                    }
                }

                if ($last_id && isset($level_array[$last_id]))
                {
                    $level = $level_array[$last_id];
                }
                else
                {
                    $level = 0;
                    break;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }

        if (!$spec_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_id]))
            {
                return array();
            }

            $spec_id_level = $options[$spec_id]['level'];

            foreach ($options AS $key => $value)
            {
                if ($key != $spec_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }

            $spec_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_id_level == $value['level'] && $value[$id_alias] != $spec_id) ||
                    ($spec_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_id] = $spec_id_array;

            return $spec_id_array;
        }
    }
}

if (!function_exists('getOrderBy'))
{
    //根据tags-list规则，获取查询排序，用于标签文件 TagArclist / TagList
    function getOrderBy($orderby,$orderWay,$isrand=false){
        switch ($orderby) {
            case 'hot':
            case 'click':
                $orderby = "a.click {$orderWay}";
                break;

            case 'id': // 兼容织梦的写法
            case 'aid':
                $orderby = "a.aid {$orderWay}";
                break;

            case 'now':
            case 'new': // 兼容织梦的写法
            case 'pubdate': // 兼容织梦的写法
            case 'add_time':
                $orderby = "a.add_time {$orderWay}";
                break;

            case 'sortrank': // 兼容织梦的写法
            case 'sort_order':
                $orderby = "a.sort_order {$orderWay}";
                break;

            case 'rand':
                if (true === $isrand) {
                    $orderby = "rand()";
                } else {
                    $orderby = "a.aid {$orderWay}";
                }
                break;

            default:
            {
                if (empty($orderby)) {
                    $orderby = 'a.sort_order asc, a.aid desc';
                } elseif (trim($orderby) != 'rand()') {
                    $orderbyArr = explode(',', $orderby);
                    foreach ($orderbyArr as $key => $val) {
                        $val = trim($val);
                        if (preg_match('/^([a-z]+)\./i', $val) == 0) {
                            $val = 'a.'.$val;
                            $orderbyArr[$key] = $val;
                        }
                    }
                    $orderby = implode(',', $orderbyArr);
                }
                break;
            }
        }

        return $orderby;
    }
}
if (!function_exists('is_local_images')) 
{
    /**
     * 判断远程链接是否属于本地图片，并返回本地图片路径
     * @param string $pic_url 图片地址
     * @param boolean $returnbool 返回类型，false 返回图片路径，true 返回布尔值
     */
    function is_local_images($pic_url = '', $returnbool = false)
    {
		global $_M;
		$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : \think\facade\Request::rootUrl();
        $picPath  = parse_url($pic_url, PHP_URL_PATH);
        // if (preg_match('/^([^:]*):?\/\/([^\/]+)(.*)\/(uploads\/allimg|upload)\/(.*)\.([^\.]+)$/i', $pic_url) && file_exists('.'.$picPath)) {
        if (!empty($picPath) && file_exists('.'.$picPath)) {
            $picPath = preg_replace('#^'.$root_dir.'/#i', '/', $picPath);
            $pic_url = $root_dir.$picPath;
            if (true == $returnbool) {
                return $pic_url;
            }
        }

        if (true == $returnbool) {
            return false;
        } else {
            return $pic_url;
        }
    }
}
if (!function_exists('thumb_img')) 
{
    /**
     * 缩略图 从原始图来处理出来
     * @param string $original_img  图片路径
     * @param int    $width     生成缩略图的宽度
     * @param int    $height    生成缩略图的高度
     * @param string $thumb_mode    生成方式
     */
    function thumb_img($original_img = '', $width = '', $height = '', $thumb_mode = '')
    {
		global $_M;
		$root_dir = isset($_M['root_dir']) ? $_M['root_dir'] : \think\facade\Request::rootUrl();
        // 缩略图配置
        static $thumbConfig = null;
        null === $thumbConfig && $thumbConfig = config('tpcache');
        $thumbextra = config('global.thumb');

        if (!empty($width) || !empty($height) || !empty($thumb_mode)) { // 单独在模板里调用，不受缩略图全局开关影响

        } else { // 非单独模板调用，比如内置的arclist\list标签里
            if (empty($thumbConfig['thumb_open'])) {
                return $original_img;
            }
        }

        // 缩略图优先级别高于七牛云，自动把七牛云的图片路径转为本地图片路径，并且进行缩略图
        $original_img = is_local_images($original_img);

        // 未开启缩略图，或远程图片
        if (is_http_url($original_img) || stristr($original_img, '/static/common/images/not_adv.jpg')) {
            return $original_img;
        } else if (empty($original_img)) {
            return $root_dir.'/static/common/images/not_adv.jpg';
        }

        // 图片文件名
        $filename = '';
        $imgArr = explode('/', $original_img);    
        $imgArr = end($imgArr);
        $filename = preg_replace("/\.([^\.]+)$/i", "", $imgArr);
        $file_ext = preg_replace("/^(.*)\.([^\.]+)$/i", "$2", $imgArr);

        // 如果图片参数是缩略图，则直接获取到原图，并进行缩略处理
        if (preg_match('/\/uploads\/thumb\/\d{1,}_\d{1,}\//i', $original_img)) {
            $pattern = 'uploads/allimg/*/'.$filename;
            if (in_array(strtolower($file_ext), ['jpg','jpeg'])) {
                $pattern .= '.jp*g';
            } else {
                $pattern .= '.'.$file_ext;
            }
            $original_img_tmp = glob($pattern);
            if (!empty($original_img_tmp)) {
                $original_img = '/'.current($original_img_tmp);
            }
        } else {
            if ('bmp' == $file_ext && version_compare(PHP_VERSION,'7.2.0','<')) {
                return $original_img;
            }
        }
        // --end

        $original_img1 = preg_replace('#^'.$root_dir.'#i', '', handle_subdir($original_img));
        $original_img1 = '.' . $original_img1; // 相对路径
        //获取图像信息
        $info = @getimagesize($original_img1);
        //检测图像合法性
        if (false === $info || (IMAGETYPE_GIF === $info[2] && empty($info['bits']))) {
            return $original_img;
        } else {
            if (!empty($info['mime']) && stristr($info['mime'], 'bmp') && version_compare(PHP_VERSION,'7.2.0','<')) {
                return $original_img;
            }
        }

        // 缩略图宽高度
        empty($width) && $width = !empty($thumbConfig['thumb_width']) ? $thumbConfig['thumb_width'] : $thumbextra['width'];
        empty($height) && $height = !empty($thumbConfig['thumb_height']) ? $thumbConfig['thumb_height'] : $thumbextra['height'];
        $width = intval($width);
        $height = intval($height);

        //判断缩略图是否存在
        $path = "uploads/thumb/{$width}_{$height}/";
        $img_thumb_name = "{$filename}";

        // 已经生成过这个比例的图片就直接返回了
        if (is_file($path . $img_thumb_name . '.jpg')) return $root_dir.'/' . $path . $img_thumb_name . '.jpg';
        if (is_file($path . $img_thumb_name . '.jpeg')) return $root_dir.'/' . $path . $img_thumb_name . '.jpeg';
        if (is_file($path . $img_thumb_name . '.gif')) return $root_dir.'/' . $path . $img_thumb_name . '.gif';
        if (is_file($path . $img_thumb_name . '.png')) return $root_dir.'/' . $path . $img_thumb_name . '.png';
        if (is_file($path . $img_thumb_name . '.bmp')) return $root_dir.'/' . $path . $img_thumb_name . '.bmp';

        if (!is_file($original_img1)) {
            return $root_dir.'/static/common/images/not_adv.jpg';
        }

        try {
            
            $image = \think\Image::open($original_img1);

            $img_thumb_name = $img_thumb_name . '.' . $image->type();
            // 生成缩略图
            !is_dir($path) && mkdir($path, 0777, true);
            // 填充颜色
            $thumb_color = !empty($thumbConfig['thumb_color']) ? $thumbConfig['thumb_color'] : $thumbextra['color'];
            // 生成方式参考 vendor/topthink/think-image/src/Image.php
            if (!empty($thumb_mode)) {
                $thumb_mode = intval($thumb_mode);
            } else {
                $thumb_mode = !empty($thumbConfig['thumb_mode']) ? $thumbConfig['thumb_mode'] : $thumbextra['mode'];
            }
            1 == $thumb_mode && $thumb_mode = 6; // 按照固定比例拉伸
            2 == $thumb_mode && $thumb_mode = 2; // 填充空白
            if (3 == $thumb_mode) {
                $img_width = $image->width();
                $img_height = $image->height();
                if ($width < $img_width && $height < $img_height) {
                    // 先进行缩略图等比例缩放类型，取出宽高中最小的属性值
                    $min_width = ($img_width < $img_height) ? $img_width : 0;
                    $min_height = ($img_width > $img_height) ? $img_height : 0;
                    if ($min_width > $width || $min_height > $height) {
                        if (0 < intval($min_width)) {
                            $scale = $min_width / min($width, $height);
                        } else if (0 < intval($min_height)) {
                            $scale = $min_height / $height;
                        } else {
                            $scale = $min_width / $width;
                        }
                        $s_width  = $img_width / $scale;
                        $s_height = $img_height / $scale;
                        $image->thumb($s_width, $s_height, 1, $thumb_color)->save($path . $img_thumb_name, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
                    }
                }
                $thumb_mode = 3; // 截减
            }
            // 参考文章 http://www.mb5u.com/biancheng/php/php_84533.html  改动参考 http://www.thinkphp.cn/topic/13542.html
            $image->thumb($width, $height, $thumb_mode, $thumb_color)->save($path . $img_thumb_name, NULL, 100); //按照原图的比例生成一个最大为$width*$height的缩略图并保存
            //图片水印处理
            $water = tpCache('water');
            if($water['is_mark']==1 && $water['is_thumb_mark'] == 1 && $image->width()>$water['mark_width'] && $image->height()>$water['mark_height']){
                $imgresource = '.' . $root_dir . '/' . $path . $img_thumb_name;
                if($water['mark_type'] == 'text'){
                    //$image->text($water['mark_txt'],root_path('data').'font/hgzb.ttf',20,'#000000',9)->save($imgresource);
                    $ttf = root_path('data').'font/hgzb.ttf';
                    if (file_exists($ttf)) {
                        $size = $water['mark_txt_size'] ? $water['mark_txt_size'] : 30;
                        $color = $water['mark_txt_color'] ?: '#000000';
                        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
                            $color = '#000000';
                        }
                        $transparency = intval((100 - $water['mark_degree']) * (127/100));
                        $color .= dechex($transparency);
                        $image->open($imgresource)->text($water['mark_txt'], $ttf, $size, $color, $water['mark_sel'])->save($imgresource);
                        $return_data['mark_txt'] = $water['mark_txt'];
                    }
                }else{
                    /*支持子目录*/
                    $water['mark_img'] = preg_replace('#^(/[/\w]+)?(/upload/|/uploads/)#i', '$2', $water['mark_img']); // 支持子目录
                    /*--end*/
                    //$image->water(".".$water['mark_img'],9,$water['mark_degree'])->save($imgresource);
                    $waterPath = "." . $water['mark_img'];
                    if (eyPreventShell($waterPath) && file_exists($waterPath)) {
                        $quality = $water['mark_quality'] ? $water['mark_quality'] : 80;
                        $waterTempPath = dirname($waterPath).'/temp_'.basename($waterPath);
                        $image->open($waterPath)->save($waterTempPath, null, $quality);
                        $image->open($imgresource)->water($waterTempPath, $water['mark_sel'], $water['mark_degree'])->save($imgresource);
                        @unlink($waterTempPath);
                    }
                }
            }
            $img_url = $root_dir.'/' . $path . $img_thumb_name;

            return $img_url;

        } catch (\Exception $e) {

            return $original_img;
        }
    }
}



