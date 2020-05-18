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
// [ 内容 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace app\home\controller;

use think\facade\Db;
class View extends BaseController
{
    /**
     * 模型标识
     * @var string
     */
    protected $nid = '';
    /**
     * 模型ID
     * @var string
     */
    protected $channel = '';
    /**
     * 模型名称
     * @var string
     */
    protected $modelName = '';
    /**
     * 内容页
     */
    public function index($aid = '')
    {
        if (!is_numeric($aid) || strval(intval($aid)) !== strval($aid)) {
            return abort(404, '页面不存在');
        }
        $aid = intval($aid);
        $archivesInfo = Db::name('archives')
		->field('a.typeid, a.channel, b.nid, b.ctl_name')
		->alias('a')
		->join('channeltype b', 'a.channel = b.id', 'LEFT')
		->where(['a.aid' => $aid, 'a.is_del' => 0])
		->getOne();
        if (empty($archivesInfo) || !in_array($archivesInfo['channel'], config('global.allow_release_channel'))) {
            return abort(404, '页面不存在');
        }
        $this->nid = $archivesInfo['nid'];
        $this->channel = $archivesInfo['channel'];
        $this->modelName = $archivesInfo['ctl_name'];
        $result = S($this->modelName)->getInfo($aid);
        // 若是管理员则不受限制
        if (session('?admin_id')) {
            if ($result['arcrank'] == -1 && $result['users_id'] != session('users_id')) {
                return $this->success('待审核稿件，你没有权限阅读！');
            }
        }
        // 外部链接跳转
        if ($result['is_jump'] == 1) {
            return $this->redirect($result['jumplinks']);
        }
        $tid = $result['typeid'];
        $arctypeInfo = M('Arctype')->getInfo($tid);
        // 自定义字段的数据格式处理
        $arctypeInfo = $this->fieldLogic->getTableFieldList($arctypeInfo, config('global.arctype_channel_id'));
        if (!empty($arctypeInfo)) {
            // 是否有子栏目，用于标记【全部】选中状态
            $arctypeInfo['has_children'] = M('Arctype')->hasChildren($tid);
            // 文档模板文件，不指定文档模板，默认以栏目设置的为主
            empty($result['tempview']) && ($result['tempview'] = $arctypeInfo['tempview']);
            // 给没有type前缀的字段新增一个带前缀的字段，并赋予相同的值
            foreach ($arctypeInfo as $key => $val) {
                if (!preg_match('/^type/i', $key)) {
                    $key_new = 'type' . $key;
                    !array_key_exists($key_new, $arctypeInfo) && ($arctypeInfo[$key_new] = $val);
                }
            }
        } else {
            return abort(404, '页面不存在');
        }
        $result = array_merge($arctypeInfo, $result);
        // 文档链接
        $result['arcurl'] = $result['pageurl'] = '';
        if ($result['is_jump'] != 1) {
            $result['arcurl'] = $result['pageurl'] = arcurl('home/View/index',$result);
        }
        // seo
        $result['seo_title'] = set_arcseotitle($result['title'], $result['seo_title'], $result['typename']);
        $result['seo_description'] = @msubstr(checkStrHtml($result['seo_description']), 0, config('global.arc_seo_description_length'), false);
        // 支持子目录
        $result['litpic'] = handle_subdir($result['litpic']);
        // 模型对应逻辑
        $result = view_logic($aid, $this->channel, $result, true);
        // 自定义字段的数据格式处理
        $result = $this->fieldLogic->getChannelFieldList($result, $this->channel);
        $thinker = array('type' => $arctypeInfo, 'field' => $result);
        $this->thinker = array_merge($this->thinker, $thinker);
        $this->assign('thinker', $this->thinker);
        // 模板文件
        $viewfile = !empty($result['tempview']) ? str_replace('.' . $this->view_suffix, '', $result['tempview']) : 'view_' . $this->nid;
        // 若需要会员权限则执行
        if ($this->thinker['field']['arcrank'] > 0) {
            $msg = action('api/Ajax/getArcrank', array('aid' => $aid));
            if (!is_null($msg)) {
                return $this->error($msg);
            }
        }
        return $this->fetch(":{$viewfile}");
    }
    /**
     * 下载文件
     */
    public function downfile()
    {
        $file_id = input('param.id/d', 0);
        $uhash = input('param.uhash/s', '');
        if (empty($file_id) || empty($uhash)) {
            return $this->error('下载地址出错！');
        }
        @clearstatcache();
        // 查询信息
        $map = array('a.file_id' => $file_id, 'a.uhash' => $uhash);
        $result = Db::name('download_file')->alias('a')->field('a.*,b.arc_level_id')->join('archives b', 'a.aid = b.aid', 'LEFT')->where($map)->getOne();
        $file_url_gbk = iconv("utf-8", "gb2312//IGNORE", $result['file_url']);
        $file_url_gbk = preg_replace('#^(/[/\\w]+)?(/upload/soft/|/uploads/soft/)#i', '$2', $file_url_gbk);
        if (empty($result) || !is_http_url($result['file_url']) && !file_exists('.' . $file_url_gbk)) {
            return $this->error('下载文件不存在！');
        }
        // 判断会员信息
        if (0 < intval($result['arc_level_id'])) {
            $UsersData = session('users');
            if (empty($UsersData['users_id'])) {
                return $this->error('请登录后下载！');
            } else {
                // 判断会员是否可下载该文件
                // 查询会员信息
                $users = Db::name('users')
				->alias('a')
				->field('a.users_id,b.level_value,b.level_name')
				->join('users_level b', 'a.level = b.level_id', 'LEFT')
				->where(['a.users_id' => $UsersData['users_id']])
				->getOne();
                // 查询下载所需等级值
                $file_level = Db::name('archives')
				->alias('a')
				->field('b.level_value,b.level_name')
				->join('users_level b', 'a.arc_level_id = b.level_id', 'LEFT')
				->where(['a.aid' => $result['aid']])
				->getOne();
                if ($users['level_value'] < $file_level['level_value']) {
                    $msg = '文件为【' . $file_level['level_name'] . '】可下载，您当前为【' . $users['level_name'] . '】，请先升级！';
                    return $this->error($msg);
                }
                //--end
            }
        }
        // 外部下载链接
        if (is_http_url($result['file_url'])) {
            if ($result['uhash'] != md5($result['file_url'])) {
                return $this->error('下载地址出错！');
            }
            // 记录下载次数
            $this->download_log($result['file_id'], $result['aid']);
            if (isAjax()) {
                return $this->success('正在跳转中……', $result['file_url']);
            } else {
                return $this->redirect($result['file_url']);
            }
        } else {
            if (md5_file('.' . $file_url_gbk) != $result['md5file']) {
                return $this->error('下载文件包已损坏！');
            }
            // 记录下载次数
            $this->download_log($result['file_id'], $result['aid']);
            $uhash_mch = authcode($uhash, 'ENCODE');
            $url = url('home/View/downloadFile', ['file_id' => $file_id, 'uhash' => $uhash_mch]);
            if (isAjax()) {
                return $this->success('开始下载中……', $url);
            } else {
                return $this->redirect($url);
            }
        }
    }
    /**
     * 本地附件下载
     */
    public function downloadFile()
    {
        $file_id = input('param.file_id/d');
        $uhash = input('param.uhash/s', '');
        $uhash = authcode($uhash, 'DECODE');
        $map = array('file_id' => $file_id);
        $result = Db::name('download_file')->field('file_url,file_mime,uhash')->where($map)->getOne();
        if (!empty($result['uhash']) && $uhash != $result['uhash']) {
            return $this->error('下载地址出错！');
        }
        $filename = explode('/', $result['file_url']);
        $filename = end($filename);
        return download('.' . $result['file_url'])->mimeType($result['file_mime'])->name($filename)->isContent(false)->expire(360);
    }
    /**
     * 记录下载次数（重复下载不做记录，游客可重复记录）
     */
    private function download_log($file_id = 0, $aid = 0)
    {
        try {
            $users_id = session('users_id');
            $users_id = intval($users_id);
            $counts = Db::name('download_log')->where([
                //
                'file_id' => $file_id,
                'aid' => $aid,
                'users_id' => $users_id,
            ])->count();
            if (empty($users_id) || empty($counts)) {
                $saveData = [
                    //
                    'users_id' => $users_id,
                    'aid' => $aid,
                    'file_id' => $file_id,
                    'ip' => $this->request->clientIP(),
                    'add_time' => getTime(),
                ];
                $r = Db::name('download_log')->insertGetId($saveData);
                if ($r !== false) {
                    Db::name('download_file')->where(['file_id' => $file_id])->inc('downcount', 1)->update();
                    Db::name('archives')->where(['aid' => $aid])->inc('downcount', 1)->update();
                }
            }
        } catch (\Exception $e) {
        }
    }
}