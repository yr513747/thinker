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
// [ 下载附件 Model ]
// --------------------------------------------------------------------------
namespace app\home\model;

use Thinker\traits\ModelTrait;
use Thinker\basic\BaseModel;
use think\facade\Db;
//表注释: 下载附件表
//
//字段	类型	空	默认	注释
//file_id	mediumint(8)	否	 	自增ID
//aid	mediumint(8)	否	0	产品ID
//title	varchar(200)	是	 	产品标题
//file_url	varchar(255)	是	 	文件存储路径
//extract_code	varchar(20)	是	 	文件提取码
//file_size	varchar(255)	是	 	文件大小
//file_ext	varchar(50)	是	 	文件后缀名
//file_name	varchar(200)	是	 	文件名
//file_mime	varchar(200)	是	 	文件类型
//uhash	varchar(200)	是	 	自定义的一种加密方式，用于文件下载权限验证
//md5file	varchar(200)	是	 	md5_file加密，可以检测上传/下载的文件包是否损坏
//is_remote	tinyint(1)	是	0	是否远程
//downcount	int(10)	是	0	下载次数
//sort_order	smallint(5)	是	0	排序
//add_time	int(10)	是	0	上传时间
//update_time	int(11)	是	0	更新时间
class DownloadFile extends BaseModel
{
    use ModelTrait;
    protected $pk = 'file_id';
    protected $name = 'download_file';
    /**
     * 获取指定下载文章的所有文件
     */
    public static function getDownFile($aids = [], $field = '*')
    {
        $where = [];
        !empty($aids) && ($where[] = ['aid', 'IN', implode(',', $aids)]);
        $result = new self();
        $result = $result->field($field);
        !empty($where) && ($result = $result->where($where));
        $result = $result->order('sort_order asc');
        $result = $result->getArray();
        if (!empty($result)) {
            $hidden = '';
            $n = 1;
            $n2 = 1;
            foreach ($result as $key => $val) {
                //$downurl     = ROOT_DIR."/index.php?m=home&c=View&a=downfile&id={$val['file_id']}&uhash={$val['uhash']}";
                $downurl = url('home/View/downfile', array('id' => $val['file_id'], 'uhash' => $val['uhash'], '_ajax' => 1));
                $result[$key]['title'] = '';
                if (!empty($val['extract_code'])) {
                    $result[$key]['title'] = '提取码：' . $val['extract_code'];
                }
                if (is_http_url($val['file_url'])) {
                    $result[$key]['server_name'] = !empty($val['file_name']) ? $val['file_name'] : "远程服务器({$n})";
                    $n++;
                } else {
                    $result[$key]['server_name'] = "本地服务器({$n2})";
                    $n2++;
                }
                $result[$key]['softlinks'] = $downurl;
                $result[$key]['downurl'] = "javascript:thinker_1563185380({$val['file_id']});";
                $result[$key]['thinker_1563185380'] = "<input type='hidden' id='thinker_file_list_{$val['file_id']}' value='{$downurl}' />";
                $result[$key]['thinker_1563185376'] = self::handleDownJs($hidden);
            }
            $result = group_same_key($result, 'aid');
        }
        return $result;
    }
    private static function handleDownJs(&$hidden = '')
    {
        if (empty($hidden)) {
            $hidden = <<<EOF
                <script type="text/javascript">
                  function thinker_1563185380(file_id) {
                    var downurl = document.getElementById("thinker_file_list_"+file_id).value;
                    //创建异步对象
                    var ajaxObj = new XMLHttpRequest();
                    ajaxObj.open("get", downurl, true);
                    ajaxObj.setRequestHeader("X-Requested-With","XMLHttpRequest");
                    ajaxObj.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                    //发送请求
                    ajaxObj.send();
                    ajaxObj.onreadystatechange = function () {
                        // 这步为判断服务器是否正确响应
                        if (ajaxObj.readyState == 4 && ajaxObj.status == 200) {
                          var json = ajaxObj.responseText;  
                          var res = JSON.parse(json);
                          if (0 == res.code) {
                            alert(res.msg);
                          }else{
                            window.location.href = res.url;
                          }
                        } 
                    };
                  };
                </script>
EOF;
        }
        return $hidden;
    }
}