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
// [ 应用管理类 ]
// --------------------------------------------------------------------------
declare (strict_types=1);
namespace Thinker;

class AppLication
{
    /**
     * 当前应用容器
     * @var \think\App
     */
    public $app = null;
    /**
     * 当前应用实例
     * @var \think\Http
     */
    protected $http = null;
    /**
     * 请求对象
     * @var \think\Request
     */
    public $request = null;
    /**
     * 响应对象
     * @var \think\Response
     */
    protected $response = null;
    /**
     * 应用模式
     * @var string
     */
    protected $type = 'web';
    protected static $SubObject = null;
    /**
     * 构造方法
     * @param string $type 应用模式
     * @access public
     */
    public function __construct(string $type = 'web')
    {
        // 应用根目录
        defined('ROOT_PATH') or define('ROOT_PATH', null);
        // 请求对象
        defined('REQUEST_OBJECT') or define('REQUEST_OBJECT', null);
        // 应用名
        defined('APP_NAME') or define('APP_NAME', null);
        $this->type = $type;
    }
    /**
     * 初始化本类
     * @access public
     * @param string $type 应用模式
     * @return object
     */
    public static function getSubObject(string $type = 'web')
    {
        if (is_null(self::$SubObject)) {
            self::$SubObject = new static($type);
        }
        return self::$SubObject;
    }
    /**
     * 执行应用
     * @access public
     * @param string $type 应用模式
     * @return mixed
     */
    public static function runWithSubObject(string $type = 'web')
    {
        try {
            $SubObject = self::getSubObject($type);
            return $SubObject->bind($SubObject);
        } catch (\PDOException $e) {
            // TDO \PDOException
        } catch (\Exception $e) {
            throw $e;
        }
    }
    /**
     * 实例化系统应用基础类think\App，设置容器实例及应用对象实例，确保当前容器对象唯一
     * @param string $rootPath 应用根目录
     * @access public
     */
    public function setAppWithRootPath(string $rootPath = null)
    {
        $this->app = is_null($this->app) ? new App($rootPath) : $this->app;
        return $this;
    }
    /**
     * 设置请求对象
     * @access public
     * @param string $request 请求对象
     * @return $this
     */
    public function setRequest(object $request = null)
    {
        $this->request = !is_null($request) ? $request : null;
        return $this;
    }
    /**
     * 开启/关闭应用调试模式
     * @access public
     * @param bool $debug 开启/关闭应用调试模式
     * @return $this
     */
    public function debug(bool $debug = true)
    {
        $this->app->debug($debug);
        return $this;
    }
    /**
     * 从当前应用容器中获取HTTP应用对象think\Http
     * @access public
     * @return $this
     */
    public function http()
    {
        $this->http = is_null($this->http) ? $this->app->http : $this->http;
        return $this;
    }
    /**
     * 设置应用绑定
     * @access public
     * @param string $Name 应用名
     * @return $this
     */
    public function setName(string $Name)
    {
        $this->http->setName($Name);
        return $this;
    }
    /**
     * 执行HTTP应用对象的run方法启动一个HTTP应用并返回当前响应对象
     * @access public
     * @return $this
     */
    public function run()
    {
        $this->response = is_null($this->response) ? $this->http->run($this->request) : $this->response;
        return $this;
    }
    /**
     * 执行当前响应对象的send方法输出
     * @access public
     * @return $this
     */
    public function send()
    {
        $this->response->send();
        return $this;
    }
    /**
     * 执行HTTP应用对象的end方法善后
     * @access public
     * @return $this
     */
    public function end()
    {
        $this->http->end($this->response);
        return $this->afterEnd($this->app, $this->response);
    }
    protected function afterEnd(object $app, object $response)
    {
        return null;
    }
    public function bind(object $newthis = null)
    {
        switch ($this->type) {
            case 'web':
                return self::subrunhasWeb($newthis);
                break;
            case 'bind':
                return self::subrunhasBind($newthis);
                break;
            case 'text':
                return self::subrunhasText($newthis);
                break;
			case 'console':
                return self::subrunhasConsole($newthis);
                break;
            default:
                return self::subrunhasWeb($newthis);
                break;
        }
    }
    public static function subrunhasWeb(object $newthis = null)
    {
        if (is_string(APP_NAME) && APP_NAME !== null && APP_NAME !== '') {
            return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->setName(APP_NAME)->run()->send()->end();
        }
        return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->run()->send()->end();
    }
    public static function subrunhasBind(object $newthis = null)
    {
        if (is_string(APP_NAME) && APP_NAME !== null && APP_NAME !== '') {
            return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->setName(APP_NAME)->run()->send()->end();
        }
        return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->run()->send()->end();
    }
    public static function subrunhasText(object $newthis = null)
    {
        if (is_string(APP_NAME) && APP_NAME !== null && APP_NAME !== '') {
            return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->setName(APP_NAME)->run()->send()->end();
        }
        return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->http()->run()->end();
    }
	public static function subrunhasConsole(object $newthis = null)
    {
        return $newthis->setAppWithRootPath(ROOT_PATH)->debug(DEBUG_LEVEL)->setRequest(REQUEST_OBJECT)->app->console->run();
    }
}