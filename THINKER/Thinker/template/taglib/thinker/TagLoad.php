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

class TagLoad extends Base
{
    /**
     * 资源文件加载
     * @access public
     * @param  string  $file
     * @param  string  $ver
	 * @return mixed
     */
    public function getLoad($file = '', $ver = 'on')
    {
        if (empty($file)) {
            return '标签load报错：缺少属性 file 或 href 。';
        }
        $strip_space = "\r\n";
        config('template.strip_space') && ($strip_space = '');
        $version = $this->params['version'];
        $parseStr = '';
        // 文件方式导入
        $array = explode(',', $file);
        foreach ($array as $val) {
            $type = strtolower(substr(strrchr($val, '.'), 1));
            switch ($type) {
                case 'js':
                    if ($ver == 'on') {
                        $parseStr .= $this->staticVersion($val);
                    } else {
                        $parseStr .= "<script type=\"text/javascript\" src=\"{$val}?v={$version}\"></script>{$strip_space}";
                    }
                    break;
                case 'css':
                    if ($ver == 'on') {
                        $parseStr .= $this->staticVersion($val);
                    } else {
                        $parseStr .= "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$val}?v={$version}\"/>{$strip_space}";
                    }
                    break;
                case 'ico':
                    if ($ver == 'on') {
                        $parseStr .= $this->staticVersion($val);
                    } else {
                        $parseStr .= "<link rel=\"shortcut icon\" href=\"{$val}?v={$version}\"/>{$strip_space}";
                    }
                    break;
                case 'php':
                    $parseStr .= '<?php include "' . $file . '"; ?>';
                    break;
                default:
                    $parseStr .= '';
            }
        }
        return $parseStr;
    }
    /**
     * 给静态文件追加版本号，实时刷新浏览器缓存
     * @access private
     * @param    string   $file     为远程文件
     * @return   string
	 * @return mixed
     */
    private function staticVersion($file)
    {
        static $request = null;
        null == $request && ($request = $this->request);
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
                        return false;
                    }
                    $http_url = $file = $request->domain() . $this->root_dir . $filename;
                }
            }
        } else {
            if (!file_exists(realpath(ltrim($file, '/')))) {
                return false;
            }
            $http_url = $request->domain() . $this->root_dir . $file;
        }
        // -------------end---------------
        $parseStr = '';
        $headInf = @get_headers($http_url, 1);
        $update_time_str = !empty($headInf['Last-Modified']) ? '?t=' . date("Ymdhi", strtotime($headInf['Last-Modified'])) : '';
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