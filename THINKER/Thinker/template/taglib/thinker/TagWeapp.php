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
// [ 网站应用插件 ]
// --------------------------------------------------------------------------
namespace Thinker\template\taglib\thinker;

use think\facade\Db;
use Thinker\exceptions\AuthException;
class TagWeapp extends Base
{
    /**
     * 页面上展示网站应用插件
     * @access public
     * @param  string  $type
     * @return mixed
     */
    public function getWeapp($type = 'default')
    {
        $map = [];
        $map[] = ['tag_weapp', '=', '1'];
        $map[] = ['status', '=', '1'];
        $result = Db::name('weapp')->comment('页面上展示网站应用插件')->field('code,config')->where($map)->where('position', $type)->cache(true, CACHE_TIME, 'hooks')->getArray();
        foreach ($result as $key => $val) {
            $config = json_decode($val['config'], true);
            if (isMobile() && !in_array($config['scene'], [0, 1])) {
                continue;
            } else {
                if (!isMobile() && !in_array($config['scene'], [0, 2])) {
                    continue;
                }
            }
            $code = $val['code'];
            $class = $this->getWeappClass($code);
            if (class_exists($class)) {
                $weappClass = $this->app->make($class, [], true);
                if (method_exists($weappClass, 'show')) {
                    $this->hookexec("{$code}/{$code}/show");
                }
            }
        }
    }
    /**
     * 获取插件类的类名
     * @access private
     * @param strng $name 插件名
     * @param strng $controller 控制器
     * @return class
     */
    private function getWeappClass($name, $controller = '')
    {
        $controller = !empty($controller) ? $controller : $name;
        $class = "\\weapp\\{$name}\\controller\\{$controller}";
        return $class;
    }
    /**
     * 执行插件某个行为
     * @access public
     * @param  mixed  $class  要执行的行为(插件标识/控制器/操作方法)
     * @param  mixed  $params 传入的参数
     * @return mixed
     * @throws \Thinker\exceptions\AuthException
     */
    public function hookexec($class, $params = null)
    {
        $keys = "hookexec_{$class}_" . json_encode($params);
        $value = cache($keys);
        $mcaArr = explode('/', $class);
        $m = !empty($mcaArr[0]) ? $mcaArr[0] : '';
        $c = !empty($mcaArr[1]) ? $mcaArr[1] : '';
        $a = !empty($mcaArr[2]) ? $mcaArr[2] : '';
        if ($this->app->isDebug() || empty($value)) {
            $exist = Db::query('SHOW TABLES LIKE "' . $this->params['prefix'] . 'weapp"');
            if (!empty($exist)) {
                $row = Db::name('weapp')->field('id,code')->where('code', $m)->where('status', 1)->getOne();
                $value = -1;
                if (!empty($row)) {
                    if (!is_file(root_path('weapp') . $row['code'] . DIRECTORY_SEPARATOR . 'config.php')) {
                        throw new AuthException("Plug in configuration file missing");
                    }
                    $configValue = (include root_path('weapp') . $row['code'] . DIRECTORY_SEPARATOR . 'config.php');
                    $scene = intval($configValue['scene']);
                    unset($configValue);
                    if (0 == $scene) {
                        // 场景：手机端+PC端
                        $value = 1;
                    } else {
                        if (1 == $scene && isMobile()) {
                            // 场景：手机端
                            $value = 1;
                        } else {
                            if (2 == $scene && !isMobile()) {
                                // 场景：PC端
                                $value = 1;
                            }
                        }
                    }
                }
                cache($keys, $value, null, 'hook');
            }
        }
        if (1 == $value) {
          return $this->app->weappAction("{$m}/{$c}/{$a}", $params);
        }
    }
}