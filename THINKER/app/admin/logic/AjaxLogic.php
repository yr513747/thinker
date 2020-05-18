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
// [ 逻辑定义 ]
// --------------------------------------------------------------------------
declare (strict_types=1);

namespace app\admin\logic;

use Thinker\basic\BaseLogic;
use think\facade\Db;
use app\common\model\Config as ConfigModel;
class AjaxLogic extends BaseLogic
{
    

    /**
     * 进入登录页面需要异步处理的业务
     */
    public function loginHandle()
    {
        $this->saveBaseFile(); 
        $this->clearSessionFile(); 
    }

    /**
     * 进入欢迎页面需要异步处理的业务
     */
    public function welcomeHandle()
    {
        //$this->saveBaseFile(); // 存储后台入口文件路径，比如：/login.php
     
        $this->delAdminlog(); // 只保留最近三个月的操作日志
    
       // tpversion(); // 统计装载量，请勿删除，谢谢支持！
    }
    
    /**
     * 只保留最近三个月的操作日志
     */
    private function delAdminlog()
    {
        $mtime = strtotime("-1 month");
        Db::name('admin_log')->where('log_time', '<', $mtime)->delete();
       
    }

    

    /**
     * 存储后台入口文件路径，比如：/login.php
     * 在 Admin@login 和 Index@index 操作下
     */
    private function saveBaseFile()
    {
        $baseFile = $this->request->baseFile();
       
        
            ConfigModel::tpCache('web', ['web_adminbasefile'=>$baseFile]);
       
       
    }

    /**
     * 清理过期的data/session文件
     */
    private function clearSessionFile()
    {
        $path = config('session.path');
        if (!empty($path) && file_exists($path)) {
            $web_login_expiretime = ConfigModel::tpCache('web.web_login_expiretime');
            empty($web_login_expiretime) && $web_login_expiretime = config('login_expire');
            $files = glob($path.'/sess_*');
            foreach ($files as $key => $file) {
                $filemtime = filemtime($file);
                if (getTime() - intval($filemtime) > $web_login_expiretime) {
                    @unlink($file);
                }
            }
        }
    }

    

    /**
     * 升级前台会员中心的模板文件
     */
    public function updateTemplate($type = '')
    {
        if (!empty($type)) {
            if ('users' == $type) {
                if (file_exists(root_path('view').'pc/users') || file_exists(root_path('view').'mobile/users')) {
                    /*升级之前，备份涉及的源文件*/
                    $upgrade = getDirFile(root_path('data').'backup'.DS.'tpl');
                    if (!empty($upgrade) && is_array($upgrade)) {
                        delFile(root_path('data').'backup'.DS.'template_www');
                        foreach ($upgrade as $key => $val) {
                            $source_file = root_path().$val;
                            if (file_exists($source_file)) {
                                $destination_file = root_path('data').'backup'.DS.'template_www'.DS.$val;
                                tp_mkdir(dirname($destination_file));
                                @copy($source_file, $destination_file);
                            }
                        }

                        // 递归复制文件夹
                        $this->recurseCopy(root_path('data').'backup'.DS.'tpl', rtrim(root_path(), DS));
                    }
                    /*--end*/
                }
            }
        }
    }

    /**
     * 自定义函数递归的复制带有多级子目录的目录
     * 递归复制文件夹
     *
     * @param string $src 原目录
     * @param string $dst 复制到的目录
     * @return string
     */                        
    //参数说明：            
    //自定义函数递归的复制带有多级子目录的目录
    private function recurseCopy($src, $dst)
    {
        $planPath_pc = 'template/pc/';
        $planPath_m = 'template/mobile/';
        $dir = opendir($src);

        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
        $dst_tmp = str_replace('\\', '/', $dst);
        $dst_tmp = rtrim($dst_tmp, '/').'/';
        if (stristr($dst_tmp, $planPath_pc) && file_exists($planPath_pc)) {
            tp_mkdir($dst);
        } else if (stristr($dst_tmp, $planPath_m) && file_exists($planPath_m)) {
            tp_mkdir($dst);
        }
        /*--end*/

        while (false !== $file = readdir($dir)) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    $this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
                }
                else {
                    if (file_exists($src . DIRECTORY_SEPARATOR . $file)) {
                        /*pc和mobile目录存在的情况下，才拷贝会员模板到相应的pc或mobile里*/
                        $rs = true;
                        $src_tmp = str_replace('\\', '/', $src . DIRECTORY_SEPARATOR . $file);
                        if (stristr($src_tmp, $planPath_pc) && !file_exists($planPath_pc)) {
                            continue;
                        } else if (stristr($src_tmp, $planPath_m) && !file_exists($planPath_m)) {
                            continue;
                        }
                        /*--end*/
                        $rs = @copy($src . DIRECTORY_SEPARATOR . $file, $dst . DIRECTORY_SEPARATOR . $file);
                        if($rs) {
                            @unlink($src . DIRECTORY_SEPARATOR . $file);
                        }
                    }
                }
            }
        }
        closedir($dir);
    }

    // 只同步一次每个留言栏目的字段列表前4个显示(v1.5.0节点去掉)
    public function synGuestbookAttribute()
    {
        $syn_gb_attribute_showlist = ConfigModel::tpCache('syn.syn_gb_attribute_showlist');
        if (empty($syn_gb_attribute_showlist)) {
            $arctypeRow = Db::name('arctype')->field('id')->where('current_channel', 8)->getArray();
            foreach ($arctypeRow as $key => $val) {
                $attr_ids = Db::name('guestbook_attribute')->where('typeid', $val['id'])->order('attr_id asc')->limit(4)->column('attr_id');
                $attr_id = end($attr_ids);
                Db::name('guestbook_attribute')->where([
                    'typeid'    => $val['id'],
                    'attr_id'   => ['elt', intval($attr_id)],
                ])->update([
                    'is_showlist'   => 1,
                    'update_time'   => getTime(),
                ]);
            }
            ConfigModel::tpCache('syn', ['syn_gb_attribute_showlist'=>1]);
        }
    }
}
