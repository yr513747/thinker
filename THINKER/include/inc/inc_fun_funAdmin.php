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
// [ 管理员后台基本函数 ]
// --------------------------------------------------------------------------
defined('DEBUG_LEVEL') || exit;
// --------------------------------------------------------------------------
use inc\ckeditor\CKEditor;
use inc\FCKeditor\FCKeditor;
use think\exception\ClassNotFoundException;
/**
 *  获取拼音信息
 *
 * @access    public
 * @param     string  $str  字符串
 * @param     int  $ishead  是否为首字母
 * @param     int  $isclose  解析后是否释放资源
 * @return    string
 */
function SpGetPinyin($str, $ishead=0, $isclose=1)
{
    global $pinyins;
    $restr = '';
    $str = trim($str);
    $slen = strlen($str);
    if($slen < 2)
    {
        return $str;
    }
    if(count($pinyins) == 0)
    {
        $fp = fopen(INC_PATH.'/data/pinyin.dat', 'r');
        while(!feof($fp))
        {
            $line = trim(fgets($fp));
            $pinyins[$line[0].$line[1]] = substr($line, 3, strlen($line)-3);
        }
        fclose($fp);
    }
    for($i=0; $i<$slen; $i++)
    {
        if(ord($str[$i])>0x80)
        {
            $c = $str[$i].$str[$i+1];
            $i++;
            if(isset($pinyins[$c]))
            {
                if($ishead==0)
                {
                    $restr .= $pinyins[$c];
                }
                else
                {
                    $restr .= $pinyins[$c][0];
                }
            }else
            {
                $restr .= "_";
            }
        }else if( preg_match("/[a-z0-9]/i", $str[$i]) )
        {
            $restr .= $str[$i];
        }
        else
        {
            $restr .= "_";
        }
    }
    if($isclose==0)
    {
        unset($pinyins);
    }
    return $restr;
}


/**
 *  创建目录
 *
 * @access    public
 * @param     string  $spath 目录名称
 * @return    string
 */
function SpCreateDir($spath)
{
    global $cfg_dir_purview,$cfg_basedir,$cfg_ftp_mkdir,$isSafeMode;
    if($spath=='')
    {
        return true;
    }
    $flink = false;
    $truepath = $cfg_basedir;
    $truepath = str_replace("\\","/",$truepath);
    $spaths = explode("/",$spath);
    $spath = "";
    foreach($spaths as $spath)
    {
        if($spath=="")
        {
            continue;
        }
        $spath = trim($spath);
        $truepath .= "/".$spath;
        if(!is_dir($truepath) || !is_writeable($truepath))
        {
            if(!is_dir($truepath))
            {
                $isok = MkdirAll($truepath,$cfg_dir_purview);
            }
            else
            {
                $isok = ChmodAll($truepath,$cfg_dir_purview);
            }
            if(!$isok)
            {
                echo "创建或修改目录：".$truepath." 失败！<br>";
                CloseFtp();
                return false;
            }
        }
    }
    CloseFtp();
    return true;
}

function jsScript($js)
{
	$out = "<script type=\"text/javascript\">";
	$out .= "//<![CDATA[\n";
	$out .= $js;
	$out .= "\n//]]>";
	$out .= "</script>\n";

	return $out;
}

/**
 *  获取编辑器
 *
 * @access    public
 * @param     string  $fname 表单名称
 * @param     string  $fvalue 表单值
 * @param     string  $nheight 内容高度
 * @param     string  $etype 编辑器类型
 * @param     string  $gtype 获取值类型
 * @param     string  $isfullpage 是否全屏
 * @return    string
 */
function SpGetEditor($fname, $fvalue, $nheight = "350", $etype = "Basic", $gtype = "print", $isfullpage = "false", $bbcode = false)
{
    global $_M, $cfg_ckeditor_initialized;
    static $editor_number = 1;
    if (!isset($GLOBALS['cfg_fck_xhtml'])) {
        $GLOBALS['cfg_fck_xhtml'] = 'N';
    }
	if (!isset($GLOBALS['cfg_html_editor'])) {
        $GLOBALS['cfg_html_editor'] = 'ueditor';
    }
    if ($gtype == "") {
        $gtype = "print";
    }
    if ($GLOBALS['cfg_html_editor'] == 'fck' and is_dir(INC_PATH . 'FCKeditor' . DIRECTORY_SEPARATOR)) {
        // 实例化编辑器
        if (class_exists(FCKeditor::class)) {
            $fck = new FCKeditor($fname);
        } else {
            throw new ClassNotFoundException('class not exists:' . FCKeditor::class, FCKeditor::class);
        }
        $fck->BasePath = $_M['root_dir'] . '/static/plus/FCKeditor/';
        $fck->Width = '100%';
        $fck->Height = $nheight;
        $fck->ToolbarSet = $etype;
        $fck->Config['FullPage'] = $isfullpage;
        if ($GLOBALS['cfg_fck_xhtml'] == 'Y') {
            $fck->Config['EnableXHTML'] = 'true';
            $fck->Config['EnableSourceXHTML'] = 'true';
        }
        $fck->Value = $fvalue;
        if ($gtype == "print") {
            $fck->Create();
        } else {
            return $fck->CreateHtml();
        }
    } elseif ($GLOBALS['cfg_html_editor'] == 'ckeditor' and is_file(INC_PATH . 'ckeditor' . DIRECTORY_SEPARATOR . 'ckeditor.inc.php')) {
        require_once INC_PATH . 'ckeditor' . DIRECTORY_SEPARATOR . 'ckeditor.inc.php';
        // 实例化编辑器
        if (class_exists(CKEditor::class)) {
            $CKEditor = new CKEditor();
        } else {
            throw new ClassNotFoundException('class not exists:' . CKEditor::class, CKEditor::class);
        }
        $CKEditor->basePath = $_M['root_dir'] . '/static/plus/ckeditor/';
        $config = $events = array();
        $config['extraPlugins'] = 'dedepage,multipic,addon,codesnippet';
        //这里加入了代码高亮
        if ($bbcode and is_file(root_path('data') . 'smiley.data.php')) {
            $CKEditor->initialized = true;
            $config['extraPlugins'] .= ',bbcode';
            $config['fontSize_sizes'] = '30/30%;50/50%;100/100%;120/120%;150/150%;200/200%;300/300%';
            $config['disableObjectResizing'] = 'true';
            $config['smiley_path'] = $_M['root_dir'] . '/static/images/smiley/';
            // 获取表情信息
            require_once root_path('data') . 'smiley.data.php';
            $jsscript = array();
            foreach ($GLOBALS['cfg_smileys'] as $key => $val) {
                $config['smiley_images'][] = $val[0];
                $config['smiley_descriptions'][] = $val[3];
                $jsscript[] = '"' . $val[3] . '":"' . $key . '"';
            }
            $jsscript = implode(',', $jsscript);
            echo jsScript('CKEDITOR.config.ubb_smiley = {' . $jsscript . '}');
        }
        $GLOBALS['tools'] = empty($toolbar[$etype]) ? $GLOBALS['tools'] : $toolbar[$etype];
        $config['toolbar'] = $GLOBALS['tools'];
        $config['height'] = $nheight;
        //$config['skin'] = 'kama';
        $CKEditor->returnOutput = TRUE;
        $code = $CKEditor->editor($fname, $fvalue, $config, $events);
        if ($gtype == "print") {
            echo $code;
        } else {
            return $code;
        }
    } elseif ($GLOBALS['cfg_html_editor'] == 'ueditor' and is_dir(INC_PATH . 'ueditor' . DIRECTORY_SEPARATOR)) {
        //$fvalue = $fvalue == '' ? '<p></p>' : $fvalue;
		$fvalue = $fvalue == '' ? '' : $fvalue;
        $code = "";
        if ($editor_number == 1) {
            $code .= '<script type="text/javascript" charset="utf-8" src="' . $_M['root_dir'] . '/static/plus/ueditor/ueditor.config.js"></script>';
            /*$code .= '<script type="text/javascript" charset="utf-8" src="' . $_M['root_dir'] . '/static/plus/ueditor/ueditor.all.min.js"> </script>';*/
            $code .= '<script type="text/javascript" charset="utf-8" src="' . $_M['root_dir'] . '/static/plus/ueditor/ueditor.all.js"></script>';
            /*$code.='<link rel="stylesheet" type="text/css" href="'.$_M['root_dir'] . '/static/plus/ueditor/themes/default/css/ueditor.css"/>';*/
            /*建议手动加在语言，避免在ie下有时因为加载语言失败导致编辑器加载失败*/
            /*这里加载的语言文件会覆盖你在配置项目里添加的语言类型，比如你在配置项目里配置的是英文，这里加载的中文，那最后就是中文*/
            $code .= '<script type="text/javascript" charset="utf-8" src="' . $_M['root_dir'] . '/static/plus/ueditor/lang/zh-cn/zh-cn.js"></script>';
        }
        $editor_number = $editor_number + 1;
        /*$code .= '<script name="' . $fname . '" id="' . $fname . '" type="text/plain" style="width:100%;margin:0 auto;">' . $fvalue . '</script>';*/
        $code .= '<textarea name="' . $fname . '" id="' . $fname . '" style="width:100%;margin:0 auto;">' . $fvalue . '</textarea>'; 
        $code .= '<script type="text/javascript"> var ue = UE.getEditor("' . $fname . '");</script>';
        if ($gtype == "print") {
            echo $code;
        } else {
            return $code;
        }
    } else {
        throw new \RuntimeException('Unable to get editor:' . $GLOBALS['cfg_html_editor']);
    }
}
/**
 *  获取更新信息
 *
 * @return    void
 */
function SpGetNewInfo()
{
    global $cfg_version,$dsql;
    $nurl = $_SERVER['HTTP_HOST'];
    if( preg_match("#[a-z\-]{1,}\.[a-z]{2,}#i",$nurl) ) {
        $nurl = urlencode($nurl);
    }
    else {
        $nurl = "test";
    }
    $phpv = phpversion();
    $sp_os = PHP_OS;
    $mysql_ver = $dsql->GetVersion();
    $seo_info = $dsql->GetOne("SELECT * FROM `#@__plus_seoinfo` ORDER BY id DESC");
    $add_query = '';
    if ( $seo_info )
    {
        $add_query .= "&alexa_num={$seo_info['alexa_num']}&alexa_area_num={$seo_info['alexa_area_num']}&baidu_count={$seo_info['baidu_count']}&sogou_count={$seo_info['sogou_count']}&haosou360_count={$seo_info['haosou360_count']}";
    }
    $query = " SELECT COUNT(*) AS dd FROM `#@__member` ";
    $row1 = $dsql->GetOne($query);
    if ( $row1 ) $add_query .= "&mcount={$row1['dd']}";
    $query = " SELECT COUNT(*) AS dd FROM `#@__arctiny` ";
    $row2 = $dsql->GetOne($query);
    if ( $row2 ) $add_query .= "&acount={$row2['dd']}";
    
    $offUrl = "http://new"."ver.a"."pi.de"."decms.com/index.php?c=info57&version={$cfg_version}&formurl={$nurl}&phpver={$phpv}&os={$sp_os}&mysqlver={$mysql_ver}{$add_query}";
    return $offUrl;
}