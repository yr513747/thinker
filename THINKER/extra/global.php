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
// [ 系统全局配置 ]
// --------------------------------------------------------------------------
use think\facade\Cache;
use think\facade\Db;
$cacheKey = "extra_global_channeltype";
$channeltype_row = Cache::get($cacheKey);
if (empty($channeltype_row)) {
    $channeltype_row = Db::name('channeltype')->field('id,nid,ctl_name')
        ->order('id asc')
        ->select()
		->toArray();
    Cache::set($cacheKey, $channeltype_row, CACHE_TIME, "channeltype");
}

$channeltype_list = [];
$allow_release_channel = [];
foreach ($channeltype_row as $key => $val) {
    $channeltype_list[$val['nid']] = $val['id'];
    if (!in_array($val['nid'], ['guestbook','single'])) {
        array_push($allow_release_channel, $val['id']);
    }
}

return [
    // 站点维护模式，开启维护模式通过默认的入口文件访问将显示一个错误页面，详见\Thinker\traits\app\ErrorPage::setErrorPage(]方法 1为开启其他值默认关闭
    'web_status'    => 0,
    // 维护模式提示消息
    'error_message' => '网站暂时关闭，维护中……',
    // CMS根目录文件夹
    'wwwroot_dir' => ['app','config','data','errorpage','weapp','extend','extra','include','public','route','runtime','Thinker','vendor','view'],
    // 禁用栏目的目录名称
    'disable_dirname' => ['install','uploads','template','weapp','tags','search','user','users','member','reg','centre','login','cart'],
    // 发送邮箱默认有效时间，会员中心，邮箱验证时用到
    'email_default_time_out' => 3600,
    // 邮箱发送倒计时 2分钟
    'email_send_time' => 120,
    // 充值订单默认有效时间，会员中心用到，2小时
    'get_order_validity' => 7200,
    // 支付订单默认有效时间，商城中心用到，2小时
    'get_shop_order_validity' => 7200,
    // 文档SEO描述截取长度，一个字符表示一个汉字或字母
    'arc_seo_description_length' => 125,
    // 栏目最多级别
    'arctype_max_level' => 3,
    // 模型标识
    'channeltype_list' => $channeltype_list,
    // 发布文档的模型ID
    'allow_release_channel' => $allow_release_channel,
    // 广告类型
    'ad_media_type' => [
        1   => '图片',
        // 2   => 'flash',
        // 3   => '文字',
    ],
    // 仅用于产品参数
    'attr_input_type_arr' => [
        0   => '单行文本',
        2   => '多行文本',
        1   => '下拉框',
    ],
    // 仅用于留言属性
    'guestbook_attr_input_type' => [
        0   => '单行文本',
        2   => '多行文本',
        1   => '下拉框',
        3   => '单选框',
        4   => '多选框',
        5   => '单张图',
        6   => '手机号码',
        7   => 'Email邮箱',
    ],
    //留言属性正则规则管理（仅用于留言属性）
    'validate_type_list' => [
        6 => [
            'name' => '手机号码',
            'value' => '/^1\d{10}$/'
        ],
        7 => [
            'name' => 'Email邮箱',
            'value' => '/^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+]+$/'
        ],
    ],
    // 栏目自定义字段的channel_id值
    'arctype_channel_id' => -99,
    // 栏目表原始字段
    'arctype_table_fields' => ['id','channeltype','current_channel','parent_id','typename','dirname','dirpath','englist_name','grade','typelink','litpic','templist','tempview','seo_title','seo_keywords','seo_description','sort_order','is_hidden','is_part','admin_id','is_del','del_method','status','is_release','weapp_code','lang','add_time','update_time'],
    // 网络图片扩展名
    'image_ext' => 'jpg,jpeg,gif,bmp,ico,png,webp',
    // 后台语言Cookie变量
    'admin_lang' => 'admin_lang',
    // 前台语言Cookie变量
    'home_lang' => 'home_lang',
    // URL全局参数（比如：可视化uiset、多模板v、多语言lang）
    'parse_url_param'   => ['v','lang','goto'],
    // 会员金额明细类型
    'pay_cause_type_arr' => [
        0   => '升级消费',
        1   => '账户充值',
        // 2   => '后续添加',
    ],
    // 充值状态
    'pay_status_arr' => [
        // 0   => '失败',
        1   => '未付款',
        2   => '已完成',
        3   => '已充值',
        4   => '订单取消',
        // 5   => '后续添加',
    ],
    // 支付方式
    'pay_method_arr' => [
        'wechat'     => '微信',
        'alipay'     => '支付宝',
        'artificial' => '手工充值',
        'balance'    => '余额',
        'admin_pay'  => '管理员代付',
        'delivery_pay' => '货到付款',
    ],
    // 缩略图默认宽高度
    'thumb' => [
        'open'  => 0,
        'mode'  => 2,
        'color' => '#FFFFFF',
        'width' => 300,
        'height' => 300,
    ],
    // 订单状态
    'order_status_arr' => [
        -1  => '已关闭',
        0   => '待付款',
        1   => '待发货',
        2   => '待收货',
        3   => '订单完成',
        4   => '订单过期',
        // 5   => '后续添加',
    ],
    // 订单状态，后台使用
    'admin_order_status_arr' => [
        -1  => '订单关闭',
        0   => '未付款',
        1   => '待发货',
        2   => '已发货',
        3   => '已完成',
        4   => '订单过期',
    ],
    // 特殊地区(中国四个省直辖市]，目前在自定义字段控制器中使用
    'field_region_type' => ['1','338','10543','31929'],
    // 选择指定区域ID处理其他操作，目前在自定义字段控制器中使用
    'field_region_all_type' => ['-1','0','1','338','10543','31929'],
    // URL中筛选标识变量
    'url_screen_var' => 'ZXljbXM',
    // 会员投稿发布的文章状态，前台使用
    'home_article_arcrank' => [
        -1  => '未审核',
        0   => '审核通过',
    ],
    // 插件入口的问题列表
    'weapp_askanswer_list' => [
        1   => '您常用的手机号码是？',
        2   => '您常用的电子邮箱是？',
        3   => '您配偶的姓名是？',
        4   => '您初中学校名是？',
        5   => '您的出生地名是？',
        6   => '您配偶的姓名是？',
        7   => '您的身份证号后四位是？',
        8   => '您高中班主任的名字是？',
        9   => '您初中班主任的名字是？',
        10   => '您最喜欢的明星名字是？',
        11  => '对您影响最大的人名字是？',
    ],
    // 会员期限，后台使用
    'admin_member_limit_arr' => [
        1 => [
            'limit_id'   => 1,
            'limit_name' => '一周',
            'maturity_days'  => 7,
        ],
        2 => [
            'limit_id'   => 2,
            'limit_name' => '一个月',
            'maturity_days'  => 30,
        ],
        3 => [
            'limit_id'   => 3,
            'limit_name' => '三个月',
            'maturity_days'  => 90,
        ],
        4 => [
            'limit_id'   => 4,
            'limit_name' => '半年',
            'maturity_days'  => 183,
        ],
        5 => [
            'limit_id'   => 5,
            'limit_name' => '一年',
            'maturity_days'  => 366,
        ],
        6 => [
            'limit_id'   => 6,
            'limit_name' => '终身',
            'maturity_days'  => 36600,
        ],
    ],
    // 清理文件时，需要查询的数据表和字段
    'get_tablearray' => [
        0 => [
            'table' => 'ad',
            'field' => 'litpic',
        ],
        1 => [
            'table' => 'archives',
            'field' => 'litpic',
        ],
        2 => [
            'table' => 'arctype',
            'field' => 'litpic',
        ],
        3 => [
            'table' => 'images_upload',
            'field' => 'image_url',
        ],
        4 => [
            'table' => 'links',
            'field' => 'logo',
        ],
        5 => [
            'table' => 'product_img',
            'field' => 'image_url',
        ],
        6 => [
            'table' => 'ad',
            'field' => 'intro',
        ],
        7 => [
            'table' => 'article_content',
            'field' => 'content',
        ],
        8 => [
            'table' => 'download_content',
            'field' => 'content',
        ],
        9 => [
            'table' => 'images_content',
            'field' => 'content',
        ],
        10 => [
            'table' => 'product_content',
            'field' => 'content',
        ],
        11 => [
            'table' => 'single_content',
            'field' => 'content',
        ],
        12 => [
            'table' => 'config',
            'field' => 'value',
        ],
        13 => [
            'table' => 'ui_config',
            'field' => 'value',
        ],
        14 => [
            'table' => 'download_file',
            'field' => 'file_url',
        ],
        15 => [
            'table' => 'users',
            'field' => 'head_pic',
        ],
        16 => [
            'table' => 'shop_order_details',
            'field' => 'litpic',
        ],
        17 => [
            'table' => 'admin',
            'field' => 'head_pic',
        ],
        // 后续可持续添加数据表和字段，格式参照以上
    ],
];
