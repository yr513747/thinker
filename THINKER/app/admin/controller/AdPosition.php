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

class AdPosition extends Base
{
    private $ad_position_system_id = array(); // 系统默认位置ID，不可删除

    public function initialize() {
        parent::initialize();
    }

    public function index()
    {
        $list = array();
        $get = input('get.');
        $keywords = input('keywords/s');
        $condition = [];
        // 应用搜索条件
        foreach (['keywords'] as $key) {
            if (isset($get[$key]) && $get[$key] !== '') {
                if ($key == 'keywords') {
                    $condition[] = array('a.title','LIKE', "%{$get[$key]}%");
                } else {
                    $tmp_key = 'a.'.$key;
                    $condition[] = array($tmp_key,'=', $get[$key]);
                }
            }
        }

       

        $adPositionM =  M('ad_position');
        $count = $adPositionM->alias('a')->where($condition)->count();// 查询满足要求的总记录数
        $Page = new Page($count, config('paginate.list_rows'));// 实例化分页类 传入总记录数和每页显示的记录数
        $list = $adPositionM->alias('a')->where($condition)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        // 获取指定位置的广告数目
        $cid = get_arr_column($list, 'id');
        $ad_list = M('ad')->field('pid, count(id) AS has_children')
            ->where('pid','IN', $cid)
			->group('pid')
            ->getAllWithIndex('pid');
        $this->assign('ad_list', $ad_list);

        $show = $Page->show();// 分页显示输出
        $this->assign('page',$show);// 赋值分页输出
        $this->assign('list',$list);// 赋值数据集
        $this->assign('pager',$Page);// 赋值分页对象
        return $this->fetch();
    }
    
    /**
     * 新增
     */
    public function add()
    {
        //防止php超时
        function_exists('set_time_limit') && set_time_limit(0);

         

        if (isPost()) {
            $post = input('post.');

            $map = array(
                'title' => trim($post['title']),
                
            );
            if(M('ad_position')->where($map)->count() > 0){
                $this->error('该广告名称已存在，请检查', url('AdPosition/index'));
            }

            // 添加广告位置表信息
            $data = array(
                'title'       => trim($post['title']),
                'intro'       => $post['intro'],
                'admin_id'    => session('admin_id'),
              
                'add_time'    => getTime(),
                'update_time' => getTime(),
            );
            $insertId = M('ad_position')->insertGetId($data);

            if ($insertId) {
                

                // 读取组合广告位的图片及信息
                $i = '1';
                foreach ($post['img_litpic'] as $key => $value) {
                    if (!empty($value)) {
                        if (!empty($post['img_target'][$key])) {
                            $target = '1';
                        }else{
                            $target = '0';
                        }
                        // 主要参数
                        $AdData['litpic']      = $value;
                        $AdData['pid']         = $insertId;
                        $AdData['title']       = trim($post['img_title'][$key]);
                        $AdData['links']       = $post['img_links'][$key];
                        $AdData['intro']       = $post['img_intro'][$key];
                        $AdData['target']      = $target;
                        // 其他参数
                        $AdData['media_type']  = 1;
                        $AdData['admin_id']    = session('admin_id');
                       
                        $AdData['sort_order']  = $i++;
                        $AdData['add_time']    = getTime();
                        $AdData['update_time'] = getTime();
                        // 添加到广告图表
                        $ad_id = Db::name('ad')->add($AdData);
                       
                    }
                }
                adminLog('新增广告：'.$post['title']);
                $this->success("操作成功", url('AdPosition/index'));
            } else {
                $this->error("操作失败", url('AdPosition/index'));
            }
            exit;
        }

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
                if(array_key_exists($post['id'], $this->ad_position_system_id)){
                    $this->error("不可更改系统预定义位置", url('AdPosition/edit',array('id'=>$post['id'])));
                }

                $map = array();
				$map[] = array('id','NEQ', $post['id']);
				$map[] = array('title', '=' ,trim($post['title']));

                if(Db::name('ad_position')->where($map)->count() > 0){
                    $this->error('该广告名称已存在，请检查', url('AdPosition/index'));
                }

                $data = array(
                    'id'          => $post['id'],
                    'title'       => trim($post['title']),
                    'intro'       => $post['intro'],
                    'update_time' => getTime(),
                );
                $r = Db::name('ad_position')->update($data);
            }

            if ($r) {
                $i = '1';
                $ad_db = Db::name('ad');
                // 读取组合广告位的图片及信息
                foreach ($post['img_litpic'] as $key => $value) {
                    if (!empty($value)) {
                        // 是否新窗口打开
                        if (!empty($post['img_target'][$key])) {
                            $target = '1';
                        }else{
                            $target = '0';
                        }
                        // 广告位ID，为空则表示添加
                        $ad_id = $post['img_id'][$key];
                        if (!empty($ad_id)) {
                            // 查询更新条件
                            $where = [
                                'id'   => $ad_id,
                                
                            ];
                            if ($ad_db->where($where)->count() > 0) {
                                // 主要参数
                                $AdData['litpic']      = $value;
                                $AdData['title']       = $post['img_title'][$key];
                                $AdData['links']       = $post['img_links'][$key];
                                $AdData['intro']       = $post['img_intro'][$key];
                                $AdData['target']      = $target;
                                // 其他参数
                                $AdData['sort_order']  = $i++;
                                $AdData['update_time'] = getTime();
                                // 更新，不需要同步多语言
                                $ad_db->where($where)->update($AdData);
                            }else{
                                // 主要参数
                                $AdData['litpic']      = $value;
                                $AdData['pid']         = $post['id'];
                                $AdData['title']       = $post['img_title'][$key];
                                $AdData['links']       = $post['img_links'][$key];
                                $AdData['intro']       = $post['img_intro'][$key];
                                $AdData['target']      = $target;
                                // 其他参数
                                $AdData['media_type']  = 1;
                                $AdData['admin_id']    = session('admin_id');
                               
                                $AdData['sort_order']  = $i++;
                                $AdData['add_time']    = getTime();
                                $AdData['update_time'] = getTime();
                                $ad_id = $ad_db->add($AdData);
                               
                            }
                        }else{
                            // 主要参数
                            $AdData['litpic']      = $value;
                            $AdData['pid']         = $post['id'];
                            $AdData['title']       = $post['img_title'][$key];
                            $AdData['links']       = $post['img_links'][$key];
                            $AdData['intro']       = $post['img_intro'][$key];
                            $AdData['target']      = $target;
                            // 其他参数
                            $AdData['media_type']  = 1;
                            $AdData['admin_id']    = session('admin_id');
                          
                            $AdData['sort_order']  = $i++;
                            $AdData['add_time']    = getTime();
                            $AdData['update_time'] = getTime();
                            $ad_id = $ad_db->add($AdData);
                           
                        }
                    }
                }

                adminLog('编辑广告：'.$post['title']);
                $this->success("操作成功", url('AdPosition/index'));
            } else {
                $this->error("操作失败");
            }
        }

        $assign_data = array();

        $id = input('id/d');
        $field = M('ad_position')->field('a.*')
            ->alias('a')
            ->where(array('a.id'=>$id))
            ->find();
        if (empty($field)) {
            $this->error('广告不存在，请联系管理员！');
            exit;
        }
        $assign_data['field'] = $field;

        // 广告
        $ad_data = Db::name('ad')->where(array('pid'=>$field['id']))->order('sort_order asc')->select()->toArray();
        foreach ($ad_data as $key => $val) {
            $ad_data[$key]['litpic'] = handle_subdir($val['litpic']); // 支持子目录
        }
        $assign_data['ad_data'] = $ad_data;
        
        $this->assign($assign_data);
        return $this->fetch();
    }

    /**
     * 删除广告图片
     */
    public function del_imgupload()
    {
         
        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(isPost() && !empty($id_arr)){
           

            $r = Db::name('ad')->where([
                    'id' => ['IN', $id_arr],
                ])
                ->cache(true,null,'ad')
                ->delete();
            if ($r) {
               
                adminLog('删除广告-id：'.implode(',', $id_arr));
            }
        }
    }

    /**
     * 删除
     */
    public function del()
    {
         

        $id_arr = input('del_id/a');
        $id_arr = eyIntval($id_arr);
        if(isPost() && !empty($id_arr)){
            foreach ($id_arr as $key => $val) {
                if(array_key_exists($val, $this->ad_position_system_id)){
                    $this->error('系统预定义，不能删除');
                }
            }

           
            $r = M('ad_position')->where('id','IN',$id_arr)->delete();
            if ($r) {

               

                M('ad')->where('pid','IN',$id_arr)->delete();

                adminLog('删除广告-id：'.implode(',', $id_arr));
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }else{
            $this->error('参数有误');
        }
    }


}