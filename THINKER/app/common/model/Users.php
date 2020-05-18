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
namespace app\common\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
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
     * 更新会员级别信息
     * @access public
     * @param  string  $users_id  会员ID
     * @return mixed
     */
    public static function UpUsersLevelData($users_id = null)
    {
        $LevelData = [];
        // 查询系统初始的默认级别
        $LevelWhere = [
            //
            'level_id' => 1,
            'is_system' => 1,
        ];
        $level = Db::name('users_level')->where($LevelWhere)->field('level_id,level_name,level_value')->getOne();
        if (empty($level)) {
            $level = ['level' => 1, 'level_name' => '注册会员', 'level_value' => 10];
        }
        // END
        // 更新信息
        $LevelData = [
            //
            'level' => $level['level_id'],
            'open_level_time' => 0,
            'level_maturity_days' => 0,
            'update_time' => getTime(),
        ];
        $return = self::where('users_id', $users_id)->update($LevelData);
        // END
        if (!empty($return)) {
            $LevelData['level_name'] = $level['level_name'];
            $LevelData['level_value'] = $level['level_value'];
            return $LevelData;
        }
        return [];
    }
    /**
     * 会员登录之后的业务逻辑
     * @access public
     * @param  array  $users  会员信息数组
     * @return mixed
     */
    public static function loginAfter(array $users)
    {
        session('users', $users);
        session('users_id', $users['users_id']);
        cookie('users_id', $users['users_id']);
        $data = [
            //
            'last_ip' => clientIP(),
            'last_login' => getTime(),
            'login_count' => Db::raw('login_count+1'),
        ];
        self::where('users_id', $users['users_id'])->update($data);
    }
}