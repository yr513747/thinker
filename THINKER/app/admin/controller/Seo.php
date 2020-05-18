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
use think\facade\Db;
use think\facade\Cache;
use app\common\logic\ArctypeLogic;

class Seo extends Base
{

   
    
    /*
     * URL配置
     */
    public function seo()
    {
        

        $inc_type =  'seo';
        $config = tpCache($inc_type);
        $config['seo_pseudo'] = tpCache('seo.seo_pseudo');
        $seo_pseudo_list = get_seo_pseudo_list();
        $this->assign('seo_pseudo_list', $seo_pseudo_list);

        

        /* 限制文档HTML保存路径的名称 */
        $wwwroot_dir = config('global.wwwroot_dir'); // 网站根目录的目录列表
        $disable_dirname = config('global.disable_dirname'); // 栏目伪静态时的路由路径
        $wwwroot_dir = array_merge($wwwroot_dir, $disable_dirname);
        // 不能与栏目的一级目录名称重复
        $arctypeDirnames = Db::name('arctype')->where(['parent_id'=>0])->column('dirname');
        is_array($arctypeDirnames) && $wwwroot_dir = array_merge($wwwroot_dir, $arctypeDirnames);
       
       
        $wwwroot_dir = array_unique($wwwroot_dir);
        $this->assign('seo_html_arcdir_limit', implode(',', $wwwroot_dir));
        /* end */

        $seo_html_arcdir_1 = '';
        if (!empty($config['seo_html_arcdir'])) {
            $config['seo_html_arcdir'] = trim($config['seo_html_arcdir'], '/');
            $seo_html_arcdir_1 = '/'.$config['seo_html_arcdir'];
        }
        $this->assign('seo_html_arcdir_1', $seo_html_arcdir_1);

        // 栏目列表
		$map[] = ['status','=',1];
        $map[] = ['is_del','=',0];// 回收站功能
        $select_html = (new ArctypeLogic())->arctypeList(0, 0, true, config('global.arctype_max_level'), $map);
        $this->assign('select_html',$select_html);
        // 允许发布文档列表的栏目
        $arc_select_html = allow_release_arctype();
        $this->assign('arc_select_html', $arc_select_html);
       

        $this->assign('config',$config);//当前配置项
        return $this->fetch();
    }
    
    /*
     * 保存URL配置
     */
    public function handle()
    {
        if (isPost()) {
            $inc_type = 'seo';
            $param = input('post.');
            $globalConfig = $this->params['global'];
           $seo_pseudo_new = $param['seo_pseudo'];
            

            //检测是否开启pathinfo模式
            try {
                if (2 == $seo_pseudo_new) {
                    $fix_pathinfo = ini_get('cgi.fix_pathinfo');
                    if (stristr(input('server.HTTP_HOST'), '.mylightsite.com')) {
                        $this->error('腾讯云空间不支持伪静态！');
                    } else if ('' != $fix_pathinfo && 0 === $fix_pathinfo) {
                        $this->error('空间不支持伪静态，请开启pathinfo，或者在php.ini里修改cgi.fix_pathinfo=1');
                    }
                }
               
            } catch (\Exception $e) {}
           

            // 强制去除index.php
            if (isset($param['seo_force_inlet'])) {
                $seo_force_inlet = $param['seo_force_inlet'];
                $seo_force_inlet_old = !empty($globalConfig['seo_force_inlet']) ? $globalConfig['seo_force_inlet'] : '';
                if ($seo_force_inlet_old != $seo_force_inlet) {
                    $param['seo_inlet'] = $seo_force_inlet;
                }
            }
            

           
                tpCache($inc_type,$param);
         
        
            
           

            // 清空缓存
            delFile(runtime_path('html'));
            Cache::clear();
            $this->success('操作成功', url('Seo/seo'));
        }
       $this->error('操作失败');
    }

    

   
   
}