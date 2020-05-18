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
// [ 会员 Model ]
// --------------------------------------------------------------------------
namespace app\user\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
use app\common\model\UsersConfig as UsersConfigModel;
//表注释: 会员信息表
//
//字段	类型	空	默认	注释
//users_id	int(11)	否	 	表id
//username	varchar(30)	否	 	用户名
//password	varchar(32)	否	 	登录密码
//nickname	varchar(50)	否	 	昵称
//is_mobile	tinyint(1)	是	0	绑定手机号，0为不绑定，1为绑定
//mobile	varchar(20)	否	 	手机号码（仅用于登录）
//is_email	tinyint(1)	是	0	绑定邮箱，0为不绑定，1为绑定
//email	varchar(60)	否	 	电子邮件（仅用于登录）
//paypwd	varchar(50)	是	 	支付密码，暂时未用到，可保留。
//users_money	decimal(10,2)	是	0.00	用户金额
//frozen_money	decimal(10,2)	是	0.00	冻结金额
//reg_time	int(11)	是	0	注册时间
//last_login	int(11)	是	0	最后登录时间
//last_ip	varchar(15)	是	 	最后登录ip
//login_count	int(11)	是	0	登陆次数
//head_pic	varchar(255)	是	 	头像
//province	int(6)	是	0	省份
//city	int(6)	是	0	市区
//district	int(6)	是	0	县
//level	smallint(5)	是	0	会员等级
//open_level_time	int(11)	是	0	开通会员级别时间
//level_maturity_days	varchar(20)	是	 	会员级别到期天数
//discount	decimal(10,2)	是	1.00	会员折扣，默认1不享受
//total_amount	decimal(10,2)	是	0.00	消费累计额度
//is_activation	tinyint(1)	是	1	是否激活，0否，1是。 后台注册默认为1激活。 前台注册时，当会员功能设置选择后台审核，需后台激活才可以登陆。
//register_place	tinyint(1)	是	2	注册位置。后台注册不受注册验证影响，1为后台注册，2为前台注册。默认为2。
//open_id	varchar(30)	否	 	微信唯一标识openid
//is_lock	tinyint(1)	是	0	是否被锁定冻结
//admin_id	int(10)	是	0	关联管理员ID
//is_del	tinyint(1)	是	0	伪删除，1=是，0=否
//update_time	int(11)	是	0	更新时间
class Users extends BaseModel
{
    use ModelTrait;
    protected $pk = 'users_id';
    protected $name = 'users';
    /**
     * 判断会员属性中必填项是否为空
     * @param array $post_users 会员属性信息数组
     * return mixed
     */
    public static function usersIsEmpty($post_users = [])
    {
        // 会员属性
        $where = array(
            // 是否隐藏属性，0为否
            'is_hidden' => 0,
            // 是否必填属性，1为是
            'is_required' => 1,
        );
        $para_data = Db::name('users_parameter')->where($where)->field('title,name')->getArray();
        // 处理提交的属性中必填项是否为空
        foreach ($para_data as $key => $value) {
            if (isset($post_users[$value['name']])) {
                if (is_array($post_users[$value['name']])) {
                    $post_users[$value['name']] = implode(',', $post_users[$value['name']]);
                }
                $attr_value = trim($post_users[$value['name']]);
                if (empty($attr_value)) {
                    return $value['title'] . '不能为空！';
                }
            }
        }
    }
    /**
     * 判断邮箱和手机是否存在，并且判断验证码是否验证通过
     * @param array $post_users 会员属性信息数组
     * @param string $users_id 会员ID，注册时不需要传入，修改时需要传入。
     * return mixed
     */
    public static function isRequired($post_users = [], $users_id = '')
    {
        if (empty($post_users)) {
            return false;
        }
        // 处理邮箱和手机是否存在
        $where_sub = [
            //
            ['name', 'LIKE', ["email_%", "mobile_%"], 'OR'],
            ['is_system', '=', 1],
        ];
        $users_parameter = Db::name('users_parameter')->where($where_sub)->field('para_id,title,name')->getAllWithIndex('name');
        $email = '';
        $email_code = '';
        $mobile = '';
        $mobile_code = '';
        // 获取邮箱和手机号码
        foreach ($post_users as $key => $val) {
            if (preg_match('/^email_/i', $key)) {
                if (!preg_match('/_code$/i', $key)) {
                    $email = $val;
                    if (!empty($val) && !check_email($val)) {
                        return $users_parameter[$key]['title'] . '格式不正确！';
                    }
                } else {
                    $email_code = $val;
                }
            } else {
                if (preg_match('/^mobile_/i', $key)) {
                    if (!preg_match('/_code$/i', $key)) {
                        $mobile = $val;
                        if (!empty($val) && !check_mobile($val)) {
                            return $users_parameter[$key]['title'] . '格式不正确！';
                        }
                    } else {
                        $mobile_code = $val;
                    }
                }
            }
        }
        //--end
        $users_verification = UsersConfigModel::getUsersConfigData('users.users_verification');
        if ('2' == $users_verification) {
            $time = getTime();
            // 处理邮箱验证码逻辑
            if (!empty($email)) {
                $where = [
                    //
                    'email' => $email,
                    'code' => $email_code,
                ];
                !empty($users_id) && ($where['users_id'] = $users_id);
                $record = Db::name('smtp_record')->where($where)->field('record_id,status,add_time')->getOne();
                if (!empty($record)) {
                    $record['add_time'] += config('global.email_default_time_out');
                    if (1 == $record['status'] || $record['add_time'] <= $time) {
                        return '邮箱验证码已被使用或超时，请重新发送！';
                    } else {
                        // 返回后处理邮箱验证码失效操作
                        $data = [
                            // 正确
                            'code_status' => 1,
                            'email' => $email,
                        ];
                        return $data;
                    }
                } else {
                    if (!empty($users_id)) {
                        // 当会员修改邮箱地址，验证码为空或错误返回
                        $row = self::getUsersListData('email', $users_id);
                        if ($email != $row['email']) {
                            return '邮箱验证码不正确，请重新输入！';
                        }
                    } else {
                        // 当会员注册时，验证码为空或错误返回
                        return '邮箱验证码不正确，请重新输入！';
                    }
                }
            }
            //--end
        }
        $where_ruc = [];
        foreach ($users_parameter as $key => $value) {
            if (isset($post_users[$value['name']])) {
                $where_ruc[] = ['para_id', '=', $value['para_id']];
                $where_ruc[] = ['info', '=', trim($post_users[$value['name']])];
                !empty($users_id) && ($where_ruc[] = ['users_id', '<>', $users_id]);
                $users_list = Db::name('users_list')->where($where_ruc)->field('info')->getOne();
                if (!empty($users_list['info'])) {
                    return $value['title'] . '已存在！';
                }
            }
        }
    }
    /**
     * 查询会员属性信息表的邮箱和手机字段
     * @param string $users_id 会员ID
     * @param string $field 查询字段，email仅邮箱，mobile仅手机号，*为两项都查询。
     * return array
     */
    public static function getUsersListData($field, $users_id)
    {
        $Data = array();
        if ('email' == $field || '*' == $field) {
            $parawhere = [
                // 查询邮箱
                ['name', 'LIKE', "email_%"],
                ['is_system', '=', 1],
            ];
            $paraData = Db::name('users_parameter')->where($parawhere)->field('para_id')->getOne();
            $listwhere = [
                //
                'para_id' => $paraData['para_id'],
                'users_id' => $users_id,
            ];
            $listData = Db::name('users_list')->where($listwhere)->field('users_id,info')->getOne();
            $Data['email'] = $listData['info'];
        }
        if ('mobile' == $field || '*' == $field) {
            $parawhere_1 = [
                // 查询手机号
                ['name', 'LIKE', "mobile_%"],
                ['is_system', '=', 1],
            ];
            $paraData_1 = Db::name('users_parameter')->where($parawhere_1)->field('para_id')->getOne();
            $listwhere_1 = [
                //
                'para_id' => $paraData_1['para_id'],
                'users_id' => $users_id,
            ];
            $listData_1 = Db::name('users_list')->where($listwhere_1)->field('users_id,info')->getOne();
            $Data['mobile'] = $listData_1['info'];
        }
        return $Data;
    }
    /**
     * 查询解析数据表的数据用以构造from表单,用于添加,不携带数据
     * return array
     */
    public static function getDataPara()
    {
        $where = array(
            // 字段及内容数据处理
            'is_hidden' => 0,
        );
        $row = Db::name('users_parameter')->field('*')->where($where)->order('sort_order asc,para_id asc')->getArray();
        // 根据所需数据格式，拆分成一维数组
        $addonRow = array();
        // 根据不同字段类型封装数据
        $list = self::showViewFormData($row, 'users_', $addonRow);
        return $list;
    }
    /**
     * 查询解析数据表的数据用以构造from表单,用于修改,携带数据
     * @param string $users_id 会员ID
     * return array
     */
    public static function getDataParaList($users_id = '')
    {
        // 字段及内容数据处理
        $row = Db::name('users_parameter')
		->field('a.*,b.info,b.users_id')
		->alias('a')
		->join('users_list b', "a.para_id = b.para_id AND b.users_id = {$users_id}", 'LEFT')
		->where('a.is_hidden', 0)
		->order('a.sort_order asc,a.para_id asc')
		->getArray();
        // 根据所需数据格式，拆分成一维数组
        $addonRow = [];
        foreach ($row as $key => $value) {
            $addonRow[$value['name']] = $value['info'];
        }
        // 根据不同字段类型封装数据
        $list = self::showViewFormData($row, 'users_', $addonRow);
        return $list;
    }
    /**
     * 处理页面显示字段的表单数据
     * @param array $list 字段列表
     * @param array $formFieldStr 表单元素名称的统一数组前缀
     * @param array $addonRow 字段的数据
     * return array
     */
    public static function showViewFormData($list, $formFieldStr, $addonRow = array())
    {
        if (!empty($list)) {
            foreach ($list as $key => $val) {
                $val['fieldArr'] = $formFieldStr;
                switch ($val['dtype']) {
                    case 'int':
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } elseif (preg_match("#[^0-9]#", $val['dfvalue'])) {
                            $val['dfvalue'] = "";
                        }
                        break;
                    case 'float':
                    case 'decimal':
                        if (isset($addonRow[$val['name']])) {
                            $val['dfvalue'] = $addonRow[$val['name']];
                        } elseif (preg_match("#[^0-9\\.]#", $val['dfvalue'])) {
                            $val['dfvalue'] = "";
                        }
                        break;
                    case 'select':
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array();
                        }
                        break;
                    case 'radio':
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $dfTrueValue = !empty($dfvalueArr[0]) ? $dfvalueArr[0] : '';
                            $val['trueValue'] = array($dfTrueValue);
                        }
                        break;
                    case 'checkbox':
                        $dfvalue = $val['dfvalue'];
                        $dfvalueArr = explode(',', $dfvalue);
                        $val['dfvalue'] = $dfvalueArr;
                        if (isset($addonRow[$val['name']])) {
                            $val['trueValue'] = explode(',', $addonRow[$val['name']]);
                        } else {
                            $val['trueValue'] = array();
                        }
                        break;
                    case 'img':
                        $val[$val['name'] . '_thinker_is_remote'] = 0;
                        $val[$val['name'] . '_thinker_remote'] = '';
                        $val[$val['name'] . '_thinker_local'] = '';
                        if (isset($addonRow[$val['name']])) {
                            if (is_http_url($addonRow[$val['name']])) {
                                $val[$val['name'] . '_thinker_is_remote'] = 1;
                                $val[$val['name'] . '_thinker_remote'] = handle_subdir($addonRow[$val['name']]);
                            } else {
                                $val[$val['name'] . '_thinker_is_remote'] = 0;
                                $val[$val['name'] . '_thinker_local'] = handle_subdir($addonRow[$val['name']]);
                            }
                        }
                        break;
                    case 'imgs':
                        $val[$val['name'] . '_thinker_imgupload_list'] = array();
                        if (isset($addonRow[$val['name']]) && !empty($addonRow[$val['name']])) {
                            $thinker_imgupload_list = explode(',', $addonRow[$val['name']]);
                            foreach ($thinker_imgupload_list as $k1 => $v1) {
                                $thinker_imgupload_list[$k1] = handle_subdir($v1);
                            }
                            $val[$val['name'] . '_thinker_imgupload_list'] = $thinker_imgupload_list;
                        }
                        break;
                    case 'datetime':
                        $val['dfvalue'] = !empty($addonRow[$val['name']]) ? date('Y-m-d H:i:s', $addonRow[$val['name']]) : date('Y-m-d H:i:s');
                        break;
                    case 'htmltext':
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        $val['dfvalue'] = handle_subdir($val['dfvalue'], 'html');
                        break;
                    default:
                        $val['dfvalue'] = isset($addonRow[$val['name']]) ? $addonRow[$val['name']] : $val['dfvalue'];
                        if (is_string($val['dfvalue'])) {
                            $val['dfvalue'] = handle_subdir($val['dfvalue'], 'html');
                            $val['dfvalue'] = handle_subdir($val['dfvalue']);
                        }
                        break;
                }
                $list[$key] = $val;
            }
        }
        return $list;
    }
}