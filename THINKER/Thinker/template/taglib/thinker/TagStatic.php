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
// [ 资源文件加载 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

class TagStatic extends Base
{
    /**
     * 资源文件加载
     * @access public
     * @param  string  $file
     * @param  string  $href
     * @param  string  $code
	 * @return mixed
     */
    public function getStatic($file = '', $href = '', $code = '')
    {
        if (empty($file)) {
            return '标签static报错：缺少属性 file 或 href 。';
        }
        $file = !empty($href) ? $href : $file;
        static $request = null;
        null == $request && ($request = $this->request);
        $theme_style = '';
        if (isMobile() && is_dir(root_path('view') . 'mobile')) {
            $theme_style = 'mobile';
        } elseif (is_dir(root_path('view') . 'pc')) {
            $theme_style = 'pc';
        }
        $theme_style = $theme_style ? $theme_style . '/' : '';
        $parseStr = '';
        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $file = $val;
            // ---判断本地文件是否存在，否则返回false，以免@get_headers方法导致崩溃
            if ($this->isHttpUrl($file)) {
                // 判断http路径
                if (preg_match('/^http(s?):\\/\\/' . $request->host(true) . '/i', $file)) {
                    // 判断当前域名的本地服务器文件(这仅用于单台服务器，多台稍作修改便可)
                    // $pattern = '/^http(s?):\/\/([^.]+)\.([^.]+)\.([^\/]+)\/(.*)$/';
                    $pattern = '/^http(s?):\\/\\/([^\\/]+)(.*)$/';
                    preg_match_all($pattern, $file, $matches);
                    //正则表达式
                    if (!empty($matches)) {
                        $filename = $matches[count($matches) - 1][0];
                        if (!file_exists(realpath(ltrim($filename, '/')))) {
                            continue;
                        }
                        $http_url = $file = $request->domain() . $this->root_dir . $filename;
                    }
                } else {
                    // 不是本地文件禁止使用该方法
                    return $this->toHtml($file);
                }
            } else {
                if (!preg_match('/^\\//i', $file)) {
                    if (empty($code)) {
                        $file = '/template/' . $theme_style . $file;
                    } else {
                        $file = '/template/plugins/' . $code . '/' . $theme_style . $file;
                    }
                }
                if (!file_exists(ltrim($file, '/'))) {
                    continue;
                }
                $http_url = $request->domain() . $this->root_dir . $file;
                // 支持子目录
            }
            // -------------end---------------
            $headInf = @get_headers($http_url, 1);
            $update_time = !empty($headInf['Last-Modified']) ? strtotime($headInf['Last-Modified']) : '';
            $parseStr .= $this->toHtml($file, $update_time);
        }
        return $parseStr;
    }
    /**
     * 资源文件转化为html代码
     * @access private
     * @param string $file 文件路径|url路径
     * @param intval $update_time 文件时间戳
	 * @return mixed
     */
    private function toHtml($file = '', $update_time = '')
    {
        $parseStr = '';
        $file = $this->web_root . $file;
        // 支持子目录
        $update_time_str = !empty($update_time) ? '?t=' . date("Ymdhi", $update_time) : '';
        $type = strtolower(substr(strrchr($file, '.'), 1));
        $strip_space = "\r\n";
        config('template.strip_space') && ($strip_space = '');
        switch ($type) {
            case 'js':
                $parseStr .= "<script type=\"text/javascript\" src=\"{$file}{$update_time_str}\"></script>{$strip_space}";
                break;
            case 'css':
                $parseStr .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$file}{$update_time_str}\"/>{$strip_space}";
                break;
            case 'ico':
                $parseStr .= "<link rel=\"shortcut icon\" href=\"{$file}{$update_time_str}\"/>{$strip_space}";
                break;
            case 'php':
                $parseStr .= '<?php include "' . $file . '"; ?>';
                break;
            default:
                $parseStr .= '';
        }
        return $parseStr;
    }
    /**
     * 判断url是否完整的链接
     * @access private
     * @param  string $url 网址
     * @return boolean
     */
    private function isHttpUrl($url)
    {
        preg_match("/^((\\w)*:)?(\\/\\/).*\$/", $url, $match);
        if (empty($match)) {
            return false;
        } else {
            return true;
        }
    }
}