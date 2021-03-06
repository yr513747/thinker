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
use app\common\model\Channeltype;
use app\common\model\Config as ConfigModel;
use app\common\model\UsersConfig as UsersConfigModel;
use think\facade\Request;
$icon_arr = array(
    //
    'article' => 'fa fa-file-text',
    'product' => 'fa fa-cubes',
    'images' => 'fa fa-file-picture-o',
    'download' => 'fa fa-cloud-download',
    'single' => 'fa fa-bookmark-o',
    'about' => 'fa fa-minus',
    'job' => 'fa fa-minus',
    'guestbook' => 'fa fa-file-text-o',
    'feedback' => 'fa fa-file-text-o',
	'recruit' => 'fa fa-file-text-o',
);
$domain = Request::domain();
$default_words = array();
$default_addcontent = array();
// 获取有栏目的模型列表
$channel_list = Channeltype::getArctypeChannel('yes');
foreach ($channel_list as $key => $val) {
    $default_words[] = array(
        //
        'name' => $val['ntitle'],
        'action' => 'index',
        'controller' => $val['ctl_name'],
        'url' => $val['typelink'],
        'icon' => $icon_arr[$val['nid']],
    );
    if (!in_array($val['nid'], array('single', 'guestbook', 'feedback'))) {
        $default_addcontent[] = array(
            //
            'name' => $val['ntitle'],
            'action' => 'add',
            'controller' => $val['ctl_name'],
            'url' => $val['typelink'],
            'icon' => $icon_arr[$val['nid']],
        );
    }
}
/*基本信息*/
$ctlactArr = [
    //
    'System@web',
    'System@web2',
    'System@basic',
    'System@water',
    'System@smtp',
];
$system_index_arr = array();
foreach ($ctlactArr as $key => $val) {
    if (is_check_access($val)) {
        $arr = explode('@', $val);
        $system_index_arr = array('controller' => !empty($arr[0]) ? $arr[0] : '', 'action' => !empty($arr[1]) ? $arr[1] : '');
        break;
    }
}

/*SEO优化URl*/
$ctlactArr = [
    //
    'Seo@seo',
    'Sitemap@index',
    'Links@index',
];
foreach ($ctlactArr as $key => $val) {
    if (is_check_access($val)) {
        $arr = explode('@', $val);
        $seo_index_arr = array('controller' => !empty($arr[0]) ? $arr[0] : '', 'action' => !empty($arr[1]) ? $arr[1] : '');
        break;
    }
}

/*备份还原URl*/
$tools_index_arr = array('is_menu' => 1);

/*频道模型URl*/
$channeltype_index_arr = array('is_menu' => 1);

/*回收站URl*/
$recyclebin_index_arr = array('is_menu' => 1);

/*插件应用URl*/
$weapp_index_arr = array();
if (1 == ConfigModel::tpCache('web.web_weapp_switch') && is_dir(root_path('weapp'))) {
    $weapp_index_arr = array('is_menu' => 1);
}

/*会员中心URl*/
$users_index_arr = array();
if (1 == ConfigModel::tpCache('web.web_users_switch')) {
    $users_index_arr = array('is_menu' => 1, 'is_modules' => 1);
}

/*商城中心URl*/
$shop_index_arr = array();
if (1 == ConfigModel::tpCache('web.web_users_switch') && 1 == UsersConfigModel::getUsersConfigData('shop.shop_open')) {
    $shop_index_arr = array('is_menu' => 1, 'is_modules' => 1);
}

/*小程序*/
$minipro_index_arr = array();
if (1 == ConfigModel::tpCache('web.web_minipro_switch')) {
    $minipro_index_arr = array('is_modules' => 1);
}


/**
 * 权限模块属性说明
 * array
 *      id  主键ID
 *      parent_id   父ID
 *      name    模块名称
 *      controller  控制器
 *      action  操作名
 *      url     跳转链接(控制器与操作名为空时，才使用url)
 *      target  打开窗口方式
 *      icon    菜单图标
 *      grade   层级
 *      is_menu 是否显示菜单
 *      is_modules  是否显示权限模块分组
 *      is_subshowmenu  子模块是否有显示的模块
 *      child   子模块
 */
return  array(
    '1000'=>array(
        'id'=>1000,
        'parent_id'=>0,
        'name'=>'',
        'controller'=>'',
        'action'=>'',
        'url'=>'',
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'is_subshowmenu'=>1,
        'child'=>array(
            '1001' => array(
                'id'=>1001,
                'parent_id'=>1000,
                'name' => '栏目管理',
                'controller'=>'Arctype',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-sitemap',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
            '1002' => array(
                'id'=>1002,
                'parent_id'=>1000,
                'name' => '内容管理',
                'controller'=>'Archives',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-list',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
            '1003' => array(
                'id'=>1003,
                'parent_id'=>1000,
                'name' => '广告管理',
                'controller'=>'AdPosition',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-image',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
        ),
    ),
        
    '2000'=>array(
        'id'=>2000,
        'parent_id'=>0,
        'name'=>'设置',
        'controller'=>'',
        'action'=>'',
        'url'=>'', 
        'target'=>'workspace',
        'grade'=>0,
        'is_menu'=>1,
        'is_modules'=>1,
        'is_subshowmenu'=>1,
        'child'=>array(
            '2001' => array(
                'id'=>2001,
                'parent_id'=>2000,
                'name' => '基本信息',
                'controller'=>isset($system_index_arr['controller']) ? $system_index_arr['controller'] : 'System',
                'action'=>isset($system_index_arr['action']) ? $system_index_arr['action'] : 'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-cog',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>0,
                'child' => array(
                    '2001001' => array(
                        'id'=>2001001,
                        'parent_id'=>2001,
                        'name' => '网站设置',
                        'controller'=>'System',
                        'action'=>'web',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2001002' => array(
                        'id'=>2001002,
                        'parent_id'=>2001,
                        'name' => '核心设置',
                        'controller'=>'System',
                        'action'=>'web2',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2001003' => array(
                        'id'=>2001003,
                        'parent_id'=>2001,
                        'name' => '附件设置',
                        'controller'=>'System',
                        'action'=>'basic',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2001004' => array(
                        'id'=>2001004,
                        'parent_id'=>2001,
                        'name' => '图片水印',
                        'controller'=>'System',
                        'action'=>'water',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2001005' => array(
                        'id'=>2001005,
                        'parent_id'=>2001,
                        'name' => '接口配置',
                        'controller'=>'System',
                        'action'=>'smtp',
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2003' => array(
                'id'=>2003,
                'parent_id'=>2000,
                'name' => 'SEO设置',
                'controller'=>isset($seo_index_arr['controller']) ? $seo_index_arr['controller'] : 'Seo',
                'action'=>isset($seo_index_arr['action']) ? $seo_index_arr['action'] : 'seo',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-paper-plane',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>0,
                'child'=>array(
                    '2003001' => array(
                        'id'=>2003001,
                        'parent_id'=>2003,
                        'name' => 'URL配置', 
                        'controller'=>'Seo',
                        'action'=>'seo',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-newspaper-o',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2003002' => array(
                        'id'=>2003002,
                        'parent_id'=>2003,
                        'name' => 'Sitemap', 
                        'controller'=>'Sitemap',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-newspaper-o',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2003003' => array(
                        'id'=>2003003,
                        'parent_id'=>2003,
                        'name' => '友情链接', 
                        'controller'=>'Links',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-link',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2004' => array(
                'id'=>2004,
                'parent_id'=>2000,
                'name' => '高级选项',
                'controller'=>'Senior',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-code',
                'grade'=>1,
                'is_menu'=>1,
                'is_modules'=>1,
                'is_subshowmenu'=>1,
                'child' => array(
                    '2004001' => array(
                        'id'=>2004001,
                        'parent_id'=>2004,
                        'name' => '管理员', 
                        'controller'=>'Admin',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-user',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2004006' => array(
                        'id'=>2004006,
                        'parent_id'=>2004,
                        'name' => '回收站',
                        'controller'=>'RecycleBin',
                        'action'=>'arctype_index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-recycle',
                        'grade'=>2,
                        'is_menu'=>isset($recyclebin_index_arr['is_menu']) ? $recyclebin_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2004003' => array(
                        'id'=>2004003,
                        'parent_id'=>2004,
                        'name' => '模板管理', 
                        'controller'=>'Filemanager',
                        'action'=>'index', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-folder-open',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2004002' => array(
                        'id'=>2004002,
                        'parent_id'=>2004,
                        'name' => '备份还原', 
                        'controller'=>'Tools',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-database',
                        'grade'=>2,
                        'is_menu'=>isset($tools_index_arr['is_menu']) ? $tools_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    // '2004004' => array(
                    //     'id'=>2004004,
                    //     'parent_id'=>2004,
                    //     'name' => '字段管理', 
                    //     'controller'=>isset($field_cindex_arr['controller']) ? $field_cindex_arr['controller'] : '',
                    //     'action'=>isset($field_cindex_arr['action']) ? $field_cindex_arr['action'] : '',
                    //     'url'=>isset($field_cindex_arr['url']) ? $field_cindex_arr['url'] : '',
                    //     'target'=>'workspace',
                    //     'icon'=>'fa fa-cogs',
                    //     'grade'=>2,
                    //     'is_menu'=>0,
                    //     'is_modules'=>0,
                    //     'is_subshowmenu'=>0,
                    //     'child' => array(),
                    // ),
                    '2004007' => array(
                        'id'=>2004007,
                        'parent_id'=>2004,
                        'name' => '频道模型',
                        'controller'=>'Channeltype',
                        'action'=>'index',
                        'url'=>'',
                        'target'=>'workspace',
                        'icon'=>'fa fa-cube',
                        'grade'=>2,
                        'is_menu'=>isset($channeltype_index_arr['is_menu']) ? $channeltype_index_arr['is_menu'] : 0,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2004005' => array(
                        'id'=>2004005,
                        'parent_id'=>2004,
                        'name' => '清除缓存',
                        'controller'=>'System',
                        'action'=>'clear_cache', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-undo',
                        'grade'=>2,
                        'is_menu'=>1,
                        'is_modules'=>0,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                    '2004008' => array(
                        'id'=>2004008,
                        'parent_id'=>2004,
                        'name' => '功能开关',
                        'controller'=>'Index',
                        'action'=>'switch_map', 
                        'url'=>'', 
                        'target'=>'workspace',
                        'icon'=>'fa fa-toggle-on',
                        'grade'=>2,
                        'is_menu'=>0,
                        'is_modules'=>1,
                        'is_subshowmenu'=>0,
                        'child' => array(),
                    ),
                ),
            ),
            '2005' => array(
                'id'=>2005,
                'parent_id'=>2000,
                'name' => '插件应用',
                'controller'=>'Weapp',
                'action'=>'index',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-futbol-o',
                'grade'=>1,
                'is_menu'=>isset($weapp_index_arr['is_menu']) ? $weapp_index_arr['is_menu'] : 0,
                'is_modules'=>0,
                'is_subshowmenu'=>0,
                'child'=>array(),
            ),
            '2006' => array(
                'id'=>2006,
                'parent_id'=>2000,
                'name' => '会员中心',
                'controller'=>'Member',
                'action'=>'users_index',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-user',
                'grade'=>1,
                'is_menu'=>isset($users_index_arr['is_menu']) ? $users_index_arr['is_menu'] : 0,
                'is_modules'=>isset($users_index_arr['is_modules']) ? $users_index_arr['is_modules'] : 0,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
            '2008' => array(
                'id'=>2008,
                'parent_id'=>2000,
                'name' => '商城中心',
                'controller'=>'Shop',
                'action'=>'home',
                'url'=>'',
                'target'=>'workspace',
                'icon'=>'fa fa-shopping-cart',
                'grade'=>1,
                'is_menu'=>isset($shop_index_arr['is_menu']) ? $shop_index_arr['is_menu'] : 0,
                'is_modules'=>isset($shop_index_arr['is_modules']) ? $shop_index_arr['is_modules'] : 0,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
            '2009' => array(
                'id'=>2009,
                'parent_id'=>2000,
                'name' => '可视化小程序',
                'controller'=>'Minipro',
                'action'=>'index',
                'url'=>'', 
                'target'=>'workspace',
                'icon'=>'fa fa-code',
                'grade'=>1,
                'is_menu'=>0,
                'is_modules'=>isset($minipro_index_arr['is_modules']) ? $minipro_index_arr['is_modules'] : 0,
                'is_subshowmenu'=>0,
                'child' => array(),
            ),
        ),
    ),
);