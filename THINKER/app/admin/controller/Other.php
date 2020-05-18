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

namespace app\admin\controller;

use Thinker\Page;
use think\facade\Db;

class Other extends Base
{
    /*
     * 初始化操作
     */
    public function _initialize() 
    {
        parent::_initialize();
        // 判断是否有广告位置
        if (strtolower(ACTION_NAME) != 'index') {
            $count = M('ad_position')->count('id');
            if (empty($count)) {
                $this->success('缺少广告位置，正在前往中……', url('AdPosition/add'), '', 3);
                exit;
            }
        }
    }

    public function index()
    {
        $list = array();
        $get = input('get.');
        $pid = input('param.pid/d', 0);
        $keywords = input('keywords/s');
        $condition = array();
        // 应用搜索条件
        foreach (['keywords', 'pid'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition[] = array('a.title','LIKE', "%{$get[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[] = array($tmp_key,'=', $get[$key]);
                }
            }
        }$adM =  M('ad');
        $count = $adM->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $adM->alias('a')->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        /*支持子目录*/
        foreach ($list as $key => $val) {
            $val['litpic'] = handle_subdir($val['litpic']);
            $list[$key] = $val;
        }
        /*--end*/

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页对象

        $ad_position = model('AdPosition')->getAll('*','id');
        $this->assign('ad_position',$ad_position);

        $this->assign('pid',$pid);// 赋值分页对象
        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
         

        if (isPost()) {
            $post = input('post.');
            $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
            $litpic = '';
            if ($is_remote == 1) {
                $litpic = $post['litpic_remote'];
            } else {
                $litpic = $post['litpic_local'];
            }
            $newData = array(
                'litpic'            => $litpic,
                'admin_id'  => session('admin_id'),
                
                'sort_order'    => 100,
                'add_time'           => getTime(),
                'update_time'   => getTime(),
            );
            $data = array_merge($post, $newData);
            $insertId = M('ad')->insertGetId($data);

            if ($insertId) {

              

                \think\Cache::clear('ad');
                adminLog('新增广告：'.$post['title']);
                $this->success("操作成功", url('Other/index'));
            } else {
                $this->error("操作失败");
            }
            exit;
        }

        $pid = input('param.pid/d', 0);
        $this->assign('pid', $pid);

        $ad_position = model('AdPosition')->getAll('*', 'id');
        $this->assign('ad_position', $ad_position);

        $ad_media_type = config('global.ad_media_type');
        $this->assign('ad_media_type', $ad_media_type);

        return $this->fetch();
    }

    
    /**
     * 编辑
     */
    public function edit()
    {
        if (isPost()) {
            $post = input('post.');
            if(!empty($post['id'])){
                $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
                $litpic = '';
                if ($is_remote == 1) {
                    $litpic = $post['litpic_remote'];
                } else {
                    $litpic = $post['litpic_local'];
                }
                $newData = array(
                    'litpic'            => $litpic,
                    'update_time'       => getTime(),
                );
                $data = array_merge($post, $newData);
                $r = M('ad')->where([
                        'id'    => $post['id'],
                    ])
                    ->cache(true,null,'ad')
                    ->update($data);
            }
            if ($r) {
                adminLog('编辑广告');
                $this->success("操作成功", url('Other/index'));
            } else {
                $this->error("操作失败");
            }
        }

        $assign_data = array();

        $id = input('id/d');
        $field = M('ad')->where([
                'id'    => $id,
            ])->find();
        if (empty($field)) {
            $this->error('广告不存在，请联系管理员！');
            exit;
        }
        if (is_http_url($field['litpic'])) {
            $field['is_remote'] = 1;
            $field['litpic_remote'] = handle_subdir($field['litpic']);
        } else {
            $field['is_remote'] = 0;
            $field['litpic_local'] = handle_subdir($field['litpic']);
        }
        
        /*支持子目录*/
        $field['intro'] = handle_subdir($field['intro'], 'html');
        /*--end*/

        $assign_data['field'] = $field;
        $assign_data['ad_position'] = model('AdPosition')->getAll('*', 'id');

        $assign_data['ad_media_type'] = config('global.ad_media_type');

        $this->assign($assign_data);
        return $this->fetch();
    }
    
    /**
     * 删除
     */
    public function del()
    {
         

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(!empty($id_arr)){

           
            $r = M('ad')->where([
                    'id'    => ['IN', $id_arr],
                ])
                ->cache(true,null,'ad')
                ->delete();
            if ($r) {

               

                adminLog('删除广告-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }

    /**
     * ui美化新增
     */
    public function ui_add()
    {
         

        if (isPost()) {
            $post = input('post.');
            $is_remote = !empty($post['is_remote']) ? $post['is_remote'] : 0;
            $litpic = '';
            if ($is_remote == 1) {
                $litpic = $post['litpic_remote'];
            } else {
                $litpic = $post['litpic_local'];
            }
            $newData = array(
                'media_type'    => 1,
                'litpic'            => $litpic,
               
                'add_time'       => getTime(),
                'update_time'       => getTime(),
            );
            $data = array_merge($post, $newData);
            $insertId = M('ad')->insertGetId($data);
            if ($insertId) {

               

                \think\Cache::clear('ad');
                adminLog('新增广告：'.$post['title']);
                $this->success('操作成功');
            } else {
                $this->error('操作失败');
            }
        }

        $edit_id = input('param.edit_id/d', 0);
        $pid = input('param.pid/d', 0);
        
        $assign_data = array();
        $assign_data['ad_position'] = model('AdPosition')->getInfo($pid);
        $assign_data['edit_id'] = $edit_id;

        $this->assign($assign_data);
        return $this->fetch();
    }



}