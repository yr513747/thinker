<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2019 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
declare (strict_types = 1);

namespace think;

/**
 * 路由地址生成基础类
 */
abstract class AbstractUrl
{
    /**
     * 应用对象
     * @var App
     */
    protected $app;

    /**
     * 路由对象
     * @var Route
     */
    protected $route;

    /**
     * URL变量
     * @var array
     */
    protected $vars = [];

    /**
     * 路由URL
     * @var string
     */
    protected $url;

    /**
     * URL 根地址
     * @var string
     */
    protected $root = '';

    /**
     * HTTPS
     * @var bool
     */
    protected $https;

    /**
     * URL后缀
     * @var string|bool
     */
    protected $suffix = true;

    /**
     * URL域名
     * @var string|bool
     */
    protected $domain = false;
	
    protected $bindCheck;

    /**
     * 架构函数
     * @access public
     * @param  string $url URL地址
     * @param  array  $vars 参数
     */
    public function __construct(Route $route, App $app, string $url = '', array $vars = [])
    {
        $this->route = $route;
        $this->app   = $app;
        $this->url   = $url;
        $this->vars  = $vars;
    }

    /**
     * 设置URL参数
     * @access public
     * @param  array $vars URL参数
     * @return $this
     */
    public function vars(array $vars = [])
    {
        $this->vars = $vars;
        return $this;
    }

    /**
     * 设置URL后缀
     * @access public
     * @param  string|bool $suffix URL后缀
     * @return $this
     */
    public function suffix($suffix)
    {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * 设置URL域名（或者子域名）
     * @access public
     * @param  string|bool $domain URL域名
     * @return $this
     */
    public function domain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    /**
     * 设置URL 根地址
     * @access public
     * @param  string $root URL root
     * @return $this
     */
    public function root(string $root)
    {
        $this->root = $root;
        return $this;
    }

    /**
     * 设置是否使用HTTPS
     * @access public
     * @param  bool $https
     * @return $this
     */
    public function https(bool $https = true)
    {
        $this->https = $https;
        return $this;
    }

    /**
     * 检测域名
     * @access protected
     * @param  string      $url URL
     * @param  string|true $domain 域名
     * @return string
     */
    protected function parseDomain(string &$url, $domain): string
    {
        if (!$domain) {
            return '';
        }

        $request    = $this->app->request;
        $rootDomain = $request->rootDomain();

        if (true === $domain) {
            // 自动判断域名
            $domain  = $request->host();
            $domains = $this->route->rules('domain');

            if (!empty($domains)) {
                $route_domain = array_keys($domains);
                foreach ($route_domain as $domain_prefix) {
                    if (0 === strpos($domain_prefix, '*.') && strpos($domain, ltrim($domain_prefix, '*.')) !== false) {
                        foreach ($domains as $key => $rule) {
                            $rule = is_array($rule) ? $rule[0] : $rule;
                            if (is_string($rule) && false === strpos($key, '*') && 0 === strpos($url, $rule)) {
                                $url    = ltrim($url, $rule);
                                $domain = $key;

                                // 生成对应子域名
                                if (!empty($rootDomain)) {
                                    $domain .= $rootDomain;
                                }
                                break;
                            } elseif (false !== strpos($key, '*')) {
                                if (!empty($rootDomain)) {
                                    $domain .= $rootDomain;
                                }

                                break;
                            }
                        }
                    }
                }
            }
        } elseif (false === strpos($domain, '.') && 0 !== strpos($domain, $rootDomain)) {
            $domain .= '.' . $rootDomain;
        }

        if (false !== strpos($domain, '://')) {
            $scheme = '';
        } else {
            $scheme = $this->https || $request->isSsl() || $this->route->config('is_https') ? 'https://' : 'http://';
        }

        return $scheme . $domain;
    }

    /**
     * 解析URL后缀
     * @access protected
     * @param  string|bool $suffix 后缀
     * @return string
     */
    protected function parseSuffix($suffix): string
    {
        if ($suffix) {
            $suffix = true === $suffix ? $this->route->config('url_html_suffix') : $suffix;

            if (is_string($suffix) && $pos = strpos($suffix, '|')) {
                $suffix = substr($suffix, 0, $pos);
            }
        }

        return (empty($suffix) || 0 === strpos($suffix, '.')) ? (string) $suffix : '.' . $suffix;
    }

    /**
     * 直接解析URL地址
     * @access protected
     * @param  string      $url URL
     * @param  string|bool $domain Domain
     * @return string
     */
    protected function parseUrl(string $url, &$domain): string
    {
        $request = $this->app->request;
        if (0 === strpos($url, '/')) {
            // 直接作为路由地址解析
            $url = substr($url, 1);
        } elseif (false !== strpos($url, '\\')) {
            // 解析到类
            $url = ltrim(str_replace('\\', '/', $url), '/');
        } elseif (0 === strpos($url, '@')) {
            // 解析到控制器
            $url = substr($url, 1);
        } else {
            // 解析到 模块/控制器/操作
			if (method_exists($request, 'setApp')) {
                $module  = $request->app();
            } else {
                $module  = $request->app;
            }
           
            $domains = $this->route->rules('domain');
            if (true === $domain && 2 == substr_count($url, '/')) {
                $current = $request->host();
                $match   = [];
                $pos     = [];
                foreach ($domains as $key => $item) {
                    if (isset($item['[bind]']) && 0 === strpos($url, $item['[bind]'][0])) {
                        $pos[$key] = strlen($item['[bind]'][0]) + 1;
                        $match[]   = $key;
                        $module    = '';
                    }
                }
                if ($match) {
                    $domain = current($match);
                    foreach ($match as $item) {
                        if (0 === strpos($current, $item)) {
                            $domain = $item;
                        }
                    }
                    $this->bindCheck = true;
                    $url             = substr($url, $pos[$domain]);
                }
            } elseif ($domain) {
                if (isset($domains[$domain]['[bind]'][0])) {
                    $bindModule = $domains[$domain]['[bind]'][0];
                    if ($bindModule && !in_array($bindModule[0], ['\\', '@'])) {
                        $module = '';
                    }
                }
            }
            $module = $module ? $module . '/' : '';

            $controller = $request->controller();
            if ('' == $url) {
                // 空字符串输出当前的 模块/控制器/操作
                $action = $request->action();
            } else {
                $path       = explode('/', $url);
                $action     = array_pop($path);
                $controller = empty($path) ? $controller : array_pop($path);
                $module     = empty($path) ? $module : array_pop($path) . '/';
            }
            if ($this->route->config('url_convert')) {
                $action     = strtolower($action);
                $controller = static::parseName($controller);
            }
            $url = $module . $controller . '/' . $action;
        }
        return $url;
    }

    /**
     * 分析路由规则中的变量
     * @access protected
     * @param  string $rule 路由规则
     * @return array
     */
    protected function parseVar(string $rule): array
    {
        // 提取路由规则中的变量
        $var = [];

        if (preg_match_all('/<\w+\??>/', $rule, $matches)) {
            foreach ($matches[0] as $name) {
                $optional = false;

                if (strpos($name, '?')) {
                    $name     = substr($name, 1, -2);
                    $optional = true;
                } else {
                    $name = substr($name, 1, -1);
                }

                $var[$name] = $optional ? 2 : 1;
            }
        }

        return $var;
    }

    /**
     * 匹配路由地址
     * @access protected
     * @param  array $rule 路由规则
     * @param  array $vars 路由变量
     * @return mixed
     */
    public function getRuleUrl($rule, &$vars = [])
    {
        foreach ($rule as $item) {
            list($url, $pattern, $domain, $suffix) = $item;
            if (empty($pattern)) {
                return [rtrim($url, '$'), $domain, $suffix];
            }
            $type = $this->route->config('url_common_param');
            foreach ($pattern as $key => $val) {
                if (isset($vars[$key])) {
                    $url = str_replace(['[:' . $key . ']', '<' . $key . '?>', ':' . $key . '', '<' . $key . '>'], $type ? $vars[$key] : urlencode($vars[$key]), $url);
                    unset($vars[$key]);
                    $result = [$url, $domain, $suffix];
                } elseif (2 == $val) {
                    $url    = str_replace(['/[:' . $key . ']', '[:' . $key . ']', '<' . $key . '?>'], '', $url);
                    $result = [$url, $domain, $suffix];
                } else {
                    break;
                }
            }
            if (isset($result)) {
                return $result;
            }
        }
        return false;
    }

	/**
     * 解析URL
     * @return string
     */
    public function build()
    {
        $url     = $this->url;
        $suffix  = $this->suffix;
        $domain  = $this->domain;
        $request = $this->app->request;
        $vars    = $this->vars;
        if (false === $domain && $this->route->rules('domain')) {
            $domain = true;
        }
        // 解析URL
        if (0 === strpos($url, '[') && $pos = strpos($url, ']')) {
            // [name] 表示使用路由命名标识生成URL
            $name = substr($url, 1, $pos - 1);
            $url  = 'name' . substr($url, $pos + 1);
        }
        if (false === strpos($url, '://') && 0 !== strpos($url, '/')) {
            $info = parse_url($url);
            $url  = !empty($info['path']) ? $info['path'] : '';
            if (isset($info['fragment'])) {
                // 解析锚点
                $anchor = $info['fragment'];
                if (false !== strpos($anchor, '?')) {
                    // 解析参数
                    list($anchor, $info['query']) = explode('?', $anchor, 2);
                }
                if (false !== strpos($anchor, '@')) {
                    // 解析域名
                    list($anchor, $domain) = explode('@', $anchor, 2);
                }
            } elseif (strpos($url, '@') && false === strpos($url, '\\')) {
                // 解析域名
                list($url, $domain) = explode('@', $url, 2);
            }
        }

        // 解析参数
        if (is_string($vars)) {
            // aaa=1&bbb=2 转换成数组
            parse_str($vars, $vars);
        }

        if ($url) {
            $rule = $this->route->name(isset($name) ? $name : $url . (isset($info['query']) ? '?' . $info['query'] : ''));
            if (is_null($rule) && isset($info['query'])) {
                $rule = $this->route->name($url);
                // 解析地址里面参数 合并到vars
                parse_str($info['query'], $params);
                $vars = array_merge($params, $vars);
                unset($info['query']);
            }
        }
        if (!empty($rule) && $match = $this->getRuleUrl($rule, $vars)) {
            // 匹配路由命名标识
            $url = $match[0];
            // 替换可选分隔符
            $url = preg_replace(['/(\W)\?$/', '/(\W)\?/'], ['', '\1'], $url);
            if (!empty($match[1])) {
                $domain = $match[1];
            }
            if (!is_null($match[2])) {
                $suffix = $match[2];
            }
        } elseif (!empty($rule) && isset($name)) {
            throw new \InvalidArgumentException('route name not exists:' . $name);
        } else {
            // 检查别名路由
            $alias      = $this->route->rules('alias');
            $matchAlias = false;
            if ($alias) {
                // 别名路由解析
                foreach ($alias as $key => $val) {
                    if (is_array($val)) {
                        $val = $val[0];
                    }
                    if (0 === strpos($url, $val)) {
                        $url        = $key . substr($url, strlen($val));
                        $matchAlias = true;
                        break;
                    }
                }
            }
            if (!$matchAlias) {
                // 路由标识不存在 直接解析
                $url = $this->parseUrl($url, $domain);
            }
            if (isset($info['query'])) {
                // 解析地址里面参数 合并到vars
                parse_str($info['query'], $params);
                $vars = array_merge($params, $vars);
            }
        }

        // 检测URL绑定
        if (!$this->bindCheck) {
            $type = $this->route->getBind('type');
            if ($type) {
                $bind = $this->route->getBind($type);
                if ($bind && 0 === strpos($url, $bind)) {
                    $url = substr($url, strlen($bind) + 1);
                }
            }
        }
        // 还原URL分隔符
        $depr = $this->route->config('pathinfo_depr');
        $url  = str_replace('/', $depr, $url);

        // URL后缀
        $suffix = in_array($url, ['/', '']) ? '' : $this->parseSuffix($suffix);
        // 锚点
        $anchor = !empty($anchor) ? '#' . $anchor : '';
        // 参数组装
        if (!empty($vars)) {
            // 添加参数
            if ($this->route->config('url_common_param')) {
                $vars = http_build_query($vars);
                $url .= $suffix . '?' . $vars . $anchor;
            } else {
                $paramType = $this->route->config('url_param_type');
                foreach ($vars as $var => $val) {
                    if ('' !== trim($val)) {
                        if ($paramType) {
                            $url .= $depr . urlencode($val);
                        } else {
                            $url .= $depr . $var . $depr . urlencode($val);
                        }
                    }
                }
                $url .= $suffix . $anchor;
            }
        } else {
            $url .= $suffix . $anchor;
        }
        // 检测域名
        $domain = $this->parseDomain($url, $domain);
        // URL组装
        $url = $domain . rtrim($this->root, '/') . '/' . ltrim($url, '/');

        $this->bindCheck = false;
        return $url;
    }

	/**
     * 字符串命名风格转换
     * type 0 将 Java 风格转换为 C 的风格 1 将 C 风格转换为 Java 的风格
     * @access public
     * @param  string  $name    字符串
     * @param  integer $type    转换类型
     * @param  bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }

    public function __toString()
    {
        return $this->build();
    }

    public function __debugInfo()
    {
        return [
            'url'    => $this->url,
            'vars'   => $this->vars,
            'suffix' => $this->suffix,
            'domain' => $this->domain,
        ];
    }
}