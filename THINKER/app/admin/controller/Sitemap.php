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

class Sitemap extends Base
{
    public function _initialize() {
        parent::_initialize();
    }

    /*
     * Sitemap
     */
    public function index()
    {
        $inc_type =  'sitemap';
        if (isPost()) {
            $param = input('post.');
            $param['sitemap_not1'] = isset($param['sitemap_not1']) ? $param['sitemap_not1'] : 0;
            $param['sitemap_not2'] = isset($param['sitemap_not2']) ? $param['sitemap_not2'] : 0;
            $param['sitemap_xml'] = isset($param['sitemap_xml']) ? $param['sitemap_xml'] : 0;
            $param['sitemap_txt'] = isset($param['sitemap_txt']) ? $param['sitemap_txt'] : 0;
            $param['sitemap_archives_num'] = isset($param['sitemap_archives_num']) ? intval($param['sitemap_archives_num']) : 100;

            
           
                tpCache($inc_type,$param);
           
            
           
            $this->create();
            $this->success('操作成功', url('Sitemap/index'));
        }

        $config = tpCache($inc_type);
		// 临时处理数据为空
		$config['sitemap_not1'] = isset($config['sitemap_not1']) ? $config['sitemap_not1'] : 0;
            $config['sitemap_not2'] = isset($config['sitemap_not2']) ? $config['sitemap_not2'] : 0;
            $config['sitemap_xml'] = isset($config['sitemap_xml']) ? $config['sitemap_xml'] : 0;
            $config['sitemap_txt'] = isset($config['sitemap_txt']) ? $config['sitemap_txt'] : 0;
            $config['sitemap_archives_num'] = isset($config['sitemap_archives_num']) ? intval($config['sitemap_archives_num']) : 100;
        $this->assign('config',$config);
        return $this->fetch('seo/sitemap');
    }

    /**
     * 生成sitemap 
     */
    public function create()
    {
        
           sitemap_xml();
       
    }
}
