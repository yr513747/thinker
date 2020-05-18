<?php
/**
 * 易优CMS
 * ============================================================================
 * 版权所有 2016-2028 海南赞赞网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.thinkercms.com
 * ----------------------------------------------------------------------------
 * 如果商业用途务必到官方购买正版授权, 以免引起不必要的法律纠纷.
 * ============================================================================
 * Author: 小虎哥 <1105415366@qq.com>
 * Date: 2018-4-3
 */

namespace app\admin\model;

use think\facade\Db;
use think\Model;

/**
 * 留言属性
 */
class GuestbookAttribute extends Model
{
   
   


    /**
     * 验证后台列表显示 - 是否已超过4个
     */
    public function isValidate($id_name = '', $id_value = '', $field = '', $value = '')
    {
        $return = true;
        $value  = trim($value);
        $where  = [
            $id_name => $id_value,
            
        ];
        if ($value == 1 && $field == 'is_showlist') {
            $typeid          = Db::name('guestbook_attribute')->where($where)->getField('typeid');
            $where['typeid'] = $typeid;
            $count           = Db::name('guestbook_attribute')->where($where)->count();
            if ($count > 4) {
                $return = [
                    'time'=>1,
                    'msg' => '所属栏目的列表字段显示数量已达4个',
                ];
                return $return;
            }
        }
        //更新数据库
        Db::name('guestbook_attribute')->where($where)->update([
            'is_showlist'   => $value,
            'update_time'   => getTime(),
        ]);

        return $return;
    }
}