<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think;

use think\exception\HttpException;
use think\middleware\AllowCrossDomain;
use think\middleware\CheckRequestCache;
use think\middleware\FormTokenCheck;
use think\route\Url as UrlBuild;
class Route
{
	/**
     * 配置参数
     * @var array
     */
    protected $config = [
        // pathinfo分隔符
        'pathinfo_depr'         => '/',
        // 是否开启路由延迟解析
        'url_lazy_route'        => false,
        // 是否强制使用路由
        'url_route_must'        => false,
        // 合并路由规则
        'route_rule_merge'      => false,
        // 路由是否完全匹配
        'route_complete_match'  => false,
        // 去除斜杠
        'remove_slash'          => false,
        // 使用注解路由
        'route_annotation'      => false,
        // 默认的路由变量规则
        'default_route_pattern' => '[\w\.]+',
        // URL伪静态后缀
        'url_html_suffix'       => 'html',
        // 访问控制器层名称
        'controller_layer'      => 'controller',
        // 空控制器名
        'empty_controller'      => 'Error',
        // 是否使用控制器后缀
        'controller_suffix'     => false,
        // 默认控制器名
        'default_controller'    => 'Index',
        // 默认操作名
        'default_action'        => 'index',
        // 操作方法后缀
        'action_suffix'         => '',
        // 非路由变量是否使用普通参数方式（用于URL生成）
        'url_common_param'      => true,
    ];
	/**
     * 当前应用
     * @var App
     */
    protected $app;

    /**
     * 请求对象
     * @var Request
     */
    protected $request;
	
	/**
     * 当前HOST
     * @var string
     */
    protected $host;
	
    // 路由规则
    private $rules = [
        'get'     => [],
        'post'    => [],
        'put'     => [],
        'delete'  => [],
        'patch'   => [],
        'head'    => [],
        'options' => [],
        '*'       => [],
        'alias'   => [],
        'domain'  => [],
        'pattern' => [],
        'name'    => [],
    ];

    // REST路由操作方法定义
    private $rest = [
        'index'  => ['get', '', 'index'],
        'create' => ['get', '/create', 'create'],
        'edit'   => ['get', '/:id/edit', 'edit'],
        'read'   => ['get', '/:id', 'read'],
        'save'   => ['post', '', 'save'],
        'update' => ['put', '/:id', 'update'],
        'delete' => ['delete', '/:id', 'delete'],
    ];

    // 不同请求类型的方法前缀
    private $methodPrefix = [
        'get'    => 'get',
        'post'   => 'post',
        'put'    => 'put',
        'delete' => 'delete',
        'patch'  => 'patch',
    ];

    // 子域名
    private $subDomain = '';
    // 域名绑定
    private $bind = [];
    // 当前分组信息
    private $group = [];
    // 当前子域名绑定
    private $domainBind;
    private $domainRule;
    // 当前域名
    private $domain;
    // 当前路由执行过程中的参数
    private $option = [];
	
	/**
     * 路由变量
     * @var array
     */
    protected $vars = [];
	
    public function getVars()
    {
        return $this->vars;
    }
	
    public function setVars($vars = [])
    {
        $this->vars = array_merge($this->vars, $vars);
    }
	
	public function __construct(App $app)
    {
        $this->app      = $app;    
		$this->init();    
    }

    public function init()
    {
        $this->config = array_merge($this->config, $this->app->config->get('route'));

        if (!empty($this->config['middleware'])) {
            $this->app->middleware->import($this->config['middleware'], 'route');
        }
    }

    public function config(string $name = null)
    {
        if (is_null($name)) {
            return $this->config;
        }

        return $this->config[$name] ?? null;
    }

    /**
     * 注册变量规则
     * @access public
     * @param string|array $name 变量名
     * @param string       $rule 变量规则
     * @return void
     */
    public function pattern($name = null, $rule = '')
    {
        if (is_array($name)) {
            $this->rules['pattern'] = array_merge($this->rules['pattern'], $name);
        } else {
            $this->rules['pattern'][$name] = $rule;
        }
    }

    /**
     * 注册子域名部署规则
     * @access public
     * @param string|array $domain  子域名
     * @param mixed        $rule    路由规则
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function domain($domain, $rule = '', $option = [], $pattern = [])
    {
        if (is_array($domain)) {
            foreach ($domain as $key => $item) {
                $this->domain($key, $item, $option, $pattern);
            }
        } elseif ($rule instanceof \Closure) {
            // 执行闭包
            $this->setDomain($domain);
            call_user_func_array($rule, []);
            $this->setDomain(null);
        } elseif (is_array($rule)) {
            $this->setDomain($domain);
            $this->group('', function () use ($rule) {
                // 动态注册域名的路由规则
                $this->registerRules($rule);
            }, $option, $pattern);
            $this->setDomain(null);
        } else {
            $this->rules['domain'][$domain]['[bind]'] = [$rule, $option, $pattern];
        }
    }

    private function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * 设置路由绑定
     * @access public
     * @param mixed  $bind 绑定信息
     * @param string $type 绑定类型 默认为module 支持 namespace class controller
     * @return mixed
     */
    public function bind($bind, $type = 'module')
    {
        $this->bind = ['type' => $type, $type => $bind];
    }

    /**
     * 设置或者获取路由标识
     * @access public
     * @param string|array $name  路由命名标识 数组表示批量设置
     * @param array        $value 路由地址及变量信息
     * @return array
     */
    public function name($name = '', $value = null)
    {
        if (is_array($name)) {
            return $this->rules['name'] = $name;
        } elseif ('' === $name) {
            return $this->rules['name'];
        } elseif (!is_null($value)) {
            $this->rules['name'][strtolower($name)][] = $value;
        } else {
            $name = strtolower($name);
            return isset($this->rules['name'][$name]) ? $this->rules['name'][$name] : null;
        }
    }

    /**
     * 读取路由绑定
     * @access public
     * @param string $type 绑定类型
     * @return mixed
     */
    public function getBind($type)
    {
        return isset($this->bind[$type]) ? $this->bind[$type] : null;
    }

    /**
     * 导入配置文件的路由规则
     * @access public
     * @param array  $rule 路由规则
     * @param string $type 请求类型
     * @return void
     */
    public function import(array $rule, $type = '*')
    {
        // 检查域名部署
        if (isset($rule['__domain__'])) {
            $this->domain($rule['__domain__']);
            unset($rule['__domain__']);
        }

        // 检查变量规则
        if (isset($rule['__pattern__'])) {
            $this->pattern($rule['__pattern__']);
            unset($rule['__pattern__']);
        }

        // 检查路由别名
        if (isset($rule['__alias__'])) {
            $this->alias($rule['__alias__']);
            unset($rule['__alias__']);
        }

        // 检查资源路由
        if (isset($rule['__rest__'])) {
            $this->resource($rule['__rest__']);
            unset($rule['__rest__']);
        }

        $this->registerRules($rule, strtolower($type));
    }

    // 批量注册路由
    protected function registerRules($rules, $type = '*')
    {
        foreach ($rules as $key => $val) {
            if (is_numeric($key)) {
                $key = array_shift($val);
            }
            if (empty($val)) {
                continue;
            }
            if (is_string($key) && 0 === strpos($key, '[')) {
                $key = substr($key, 1, -1);
                $this->group($key, $val);
            } elseif (is_array($val)) {
                $this->setRule($key, $val[0], $type, $val[1], isset($val[2]) ? $val[2] : []);
            } else {
                $this->setRule($key, $val, $type);
            }
        }
    }

    /**
     * 注册路由规则
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param string       $type    请求类型
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function rule($rule, $route = '', $type = '*', $option = [], $pattern = [])
    {
        $group = $this->getGroup('name');

        if (!is_null($group)) {
            // 路由分组
            $option  = array_merge($this->getGroup('option'), $option);
            $pattern = array_merge($this->getGroup('pattern'), $pattern);
        }

        $type = strtolower($type);

        if (strpos($type, '|')) {
            $option['method'] = $type;
            $type             = '*';
        }
        if (is_array($rule) && empty($route)) {
            foreach ($rule as $key => $val) {
                if (is_numeric($key)) {
                    $key = array_shift($val);
                }
                if (is_array($val)) {
                    $route    = $val[0];
                    $option1  = array_merge($option, $val[1]);
                    $pattern1 = array_merge($pattern, isset($val[2]) ? $val[2] : []);
                } else {
                    $option1  = null;
                    $pattern1 = null;
                    $route    = $val;
                }
                $this->setRule($key, $route, $type, !is_null($option1) ? $option1 : $option, !is_null($pattern1) ? $pattern1 : $pattern, $group);
            }
        } else {
            $this->setRule($rule, $route, $type, $option, $pattern, $group);
        }

    }

    /**
     * 设置路由规则
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param string       $type    请求类型
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @param string       $group   所属分组
     * @return void
     */
    protected function setRule($rule, $route, $type = '*', $option = [], $pattern = [], $group = '')
    {
        if (is_array($rule)) {
            $name = $rule[0];
            $rule = $rule[1];
        } elseif (is_string($route)) {
            $name = $route;
        }
        if (!isset($option['complete_match'])) {
            if ($this->config('route_complete_match')) {
                $option['complete_match'] = true;
            } elseif ('$' == substr($rule, -1, 1)) {
                // 是否完整匹配
                $option['complete_match'] = true;
            }
        } elseif (empty($option['complete_match']) && '$' == substr($rule, -1, 1)) {
            // 是否完整匹配
            $option['complete_match'] = true;
        }

        if ('$' == substr($rule, -1, 1)) {
            $rule = substr($rule, 0, -1);
        }

        if ('/' != $rule || $group) {
            $rule = trim($rule, '/');
        }
        $vars = $this->parseVar($rule);
        if (isset($name)) {
            $key    = $group ? $group . ($rule ? '/' . $rule : '') : $rule;
            $suffix = isset($option['ext']) ? $option['ext'] : null;
            $this->name($name, [$key, $vars, $this->domain, $suffix]);
        }
        if (isset($option['modular'])) {
            $route = $option['modular'] . '/' . $route;
        }
        if ($group) {
            if ('*' != $type) {
                $option['method'] = $type;
            }
            if ($this->domain) {
                $this->rules['domain'][$this->domain]['*'][$group]['rule'][] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'pattern' => $pattern];
            } else {
                $this->rules['*'][$group]['rule'][] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'pattern' => $pattern];
            }
        } else {
            if ('*' != $type && isset($this->rules['*'][$rule])) {
                unset($this->rules['*'][$rule]);
            }
            if ($this->domain) {
                $this->rules['domain'][$this->domain][$type][$rule] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'pattern' => $pattern];
            } else {
                $this->rules[$type][$rule] = ['rule' => $rule, 'route' => $route, 'var' => $vars, 'option' => $option, 'pattern' => $pattern];
            }
            if ('*' == $type) {
                // 注册路由快捷方式
                foreach (['get', 'post', 'put', 'delete', 'patch', 'head', 'options'] as $method) {
                    if ($this->domain && !isset($this->rules['domain'][$this->domain][$method][$rule])) {
                        $this->rules['domain'][$this->domain][$method][$rule] = true;
                    } elseif (!$this->domain && !isset($this->rules[$method][$rule])) {
                        $this->rules[$method][$rule] = true;
                    }
                }
            }
        }
    }
	
	/**
     * 设置路由参数
     * @access public
     * @param  array $option 参数
     * @return $this
     */
    public function option(array $option)
    {
        $this->option = array_merge($this->option, $option);

        return $this;
    }

    /**
     * 设置当前执行的参数信息
     * @access public
     * @param array $options 参数信息
     * @return mixed
     */
    protected function setOption($options = [])
    {
        $this->option[] = $options;
    }

    /**
     * 获取当前执行的所有参数信息
     * @access public
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * 获取当前的分组信息
     * @access public
     * @param string $type 分组信息名称 name option pattern
     * @return mixed
     */
    public function getGroup($type)
    {
        if (isset($this->group[$type])) {
            return $this->group[$type];
        } else {
            return 'name' == $type ? null : [];
        }
    }

    /**
     * 设置当前的路由分组
     * @access public
     * @param string $name    分组名称
     * @param array  $option  分组路由参数
     * @param array  $pattern 分组变量规则
     * @return void
     */
    public function setGroup($name, $option = [], $pattern = [])
    {
        $this->group['name']    = $name;
        $this->group['option']  = $option ?: [];
        $this->group['pattern'] = $pattern ?: [];
    }

    /**
     * 注册路由分组
     * @access public
     * @param string|array   $name    分组名称或者参数
     * @param array|\Closure $routes  路由地址
     * @param array          $option  路由参数
     * @param array          $pattern 变量规则
     * @return void
     */
    public function group($name, $routes, $option = [], $pattern = [])
    {
        if (is_array($name)) {
            $option = $name;
            $name   = isset($option['name']) ? $option['name'] : '';
        }
        // 分组
        $currentGroup = $this->getGroup('name');
        if ($currentGroup) {
            $name = $currentGroup . ($name ? '/' . ltrim($name, '/') : '');
        }
        if (!empty($name)) {
            if ($routes instanceof \Closure) {
                $currentOption  = $this->getGroup('option');
                $currentPattern = $this->getGroup('pattern');
                $this->setGroup($name, array_merge($currentOption, $option), array_merge($currentPattern, $pattern));
                call_user_func_array($routes, []);
                $this->setGroup($currentGroup, $currentOption, $currentPattern);
                if ($currentGroup != $name) {
                    $this->rules['*'][$name]['route']   = '';
                    $this->rules['*'][$name]['var']     = $this->parseVar($name);
                    $this->rules['*'][$name]['option']  = $option;
                    $this->rules['*'][$name]['pattern'] = $pattern;
                }
            } else {
                $item          = [];
                $completeMatch = $this->config('route_complete_match');
                foreach ($routes as $key => $val) {
                    if (is_numeric($key)) {
                        $key = array_shift($val);
                    }
                    if (is_array($val)) {
                        $route    = $val[0];
                        $option1  = array_merge($option, isset($val[1]) ? $val[1] : []);
                        $pattern1 = array_merge($pattern, isset($val[2]) ? $val[2] : []);
                    } else {
                        $route = $val;
                    }

                    $options  = isset($option1) ? $option1 : $option;
                    $patterns = isset($pattern1) ? $pattern1 : $pattern;
                    if ('$' == substr($key, -1, 1)) {
                        // 是否完整匹配
                        $options['complete_match'] = true;
                        $key                       = substr($key, 0, -1);
                    } elseif ($completeMatch) {
                        $options['complete_match'] = true;
                    }
                    $key    = trim($key, '/');
                    $vars   = $this->parseVar($key);
                    $item[] = ['rule' => $key, 'route' => $route, 'var' => $vars, 'option' => $options, 'pattern' => $patterns];
                    // 设置路由标识
                    $suffix = isset($options['ext']) ? $options['ext'] : null;
                    $this->name($route, [$name . ($key ? '/' . $key : ''), $vars, $this->domain, $suffix]);
                }
                $this->rules['*'][$name] = ['rule' => $item, 'route' => '', 'var' => [], 'option' => $option, 'pattern' => $pattern];
            }

            foreach (['get', 'post', 'put', 'delete', 'patch', 'head', 'options'] as $method) {
                if (!isset($this->rules[$method][$name])) {
                    $this->rules[$method][$name] = true;
                } elseif (is_array($this->rules[$method][$name])) {
                    $this->rules[$method][$name] = array_merge($this->rules['*'][$name], $this->rules[$method][$name]);
                }
            }

        } elseif ($routes instanceof \Closure) {
            // 闭包注册
            $currentOption  = $this->getGroup('option');
            $currentPattern = $this->getGroup('pattern');
            $this->setGroup('', array_merge($currentOption, $option), array_merge($currentPattern, $pattern));
            call_user_func_array($routes, []);
            $this->setGroup($currentGroup, $currentOption, $currentPattern);
        } else {
            // 批量注册路由
            $this->rule($routes, '', '*', $option, $pattern);
        }
    }

    /**
     * 注册路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function any($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, '*', $option, $pattern);
    }

    /**
     * 注册GET路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function get($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, 'GET', $option, $pattern);
    }

    /**
     * 注册POST路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function post($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, 'POST', $option, $pattern);
    }

    /**
     * 注册PUT路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function put($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, 'PUT', $option, $pattern);
    }

    /**
     * 注册DELETE路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function delete($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, 'DELETE', $option, $pattern);
    }

    /**
     * 注册PATCH路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function patch($rule, $route = '', $option = [], $pattern = [])
    {
        $this->rule($rule, $route, 'PATCH', $option, $pattern);
    }

    /**
     * 注册资源路由
     * @access public
     * @param string|array $rule    路由规则
     * @param string       $route   路由地址
     * @param array        $option  路由参数
     * @param array        $pattern 变量规则
     * @return void
     */
    public function resource($rule, $route = '', $option = [], $pattern = [])
    {
        if (is_array($rule)) {
            foreach ($rule as $key => $val) {
                if (is_array($val)) {
                    list($val, $option, $pattern) = array_pad($val, 3, []);
                }
                $this->resource($key, $val, $option, $pattern);
            }
        } else {
            if (strpos($rule, '.')) {
                // 注册嵌套资源路由
                $array = explode('.', $rule);
                $last  = array_pop($array);
                $item  = [];
                foreach ($array as $val) {
                    $item[] = $val . '/:' . (isset($option['var'][$val]) ? $option['var'][$val] : $val . '_id');
                }
                $rule = implode('/', $item) . '/' . $last;
            }
            // 注册资源路由
            foreach ($this->rest as $key => $val) {
                if ((isset($option['only']) && !in_array($key, $option['only']))
                    || (isset($option['except']) && in_array($key, $option['except']))) {
                    continue;
                }
                if (isset($last) && strpos($val[1], ':id') && isset($option['var'][$last])) {
                    $val[1] = str_replace(':id', ':' . $option['var'][$last], $val[1]);
                } elseif (strpos($val[1], ':id') && isset($option['var'][$rule])) {
                    $val[1] = str_replace(':id', ':' . $option['var'][$rule], $val[1]);
                }
                $item           = ltrim($rule . $val[1], '/');
                $option['rest'] = $key;
                $this->rule($item . '$', $route . '/' . $val[2], $val[0], $option, $pattern);
            }
        }
    }

    /**
     * 注册控制器路由 操作方法对应不同的请求后缀
     * @access public
     * @param string $rule    路由规则
     * @param string $route   路由地址
     * @param array  $option  路由参数
     * @param array  $pattern 变量规则
     * @return void
     */
    public function controller($rule, $route = '', $option = [], $pattern = [])
    {
        foreach ($this->methodPrefix as $type => $val) {
            $this->$type($rule . '/:action', $route . '/' . $val . ':action', $option, $pattern);
        }
    }

    /**
     * 注册别名路由
     * @access public
     * @param string|array $rule   路由别名
     * @param string       $route  路由地址
     * @param array        $option 路由参数
     * @return void
     */
    public function alias($rule = null, $route = '', $option = [])
    {
        if (is_array($rule)) {
            $this->rules['alias'] = array_merge($this->rules['alias'], $rule);
        } else {
            $this->rules['alias'][$rule] = $option ? [$route, $option] : $route;
        }
    }

    /**
     * 设置不同请求类型下面的方法前缀
     * @access public
     * @param string $method 请求类型
     * @param string $prefix 类型前缀
     * @return void
     */
    public function setMethodPrefix($method, $prefix = '')
    {
        if (is_array($method)) {
            $this->methodPrefix = array_merge($this->methodPrefix, array_change_key_case($method));
        } else {
            $this->methodPrefix[strtolower($method)] = $prefix;
        }
    }

    /**
     * rest方法定义和修改
     * @access public
     * @param string|array $name     方法名称
     * @param array|bool   $resource 资源
     * @return void
     */
    public function rest($name, $resource = [])
    {
        if (is_array($name)) {
            $this->rest = $resource ? $name : array_merge($this->rest, $name);
        } else {
            $this->rest[$name] = $resource;
        }
    }

    /**
     * 注册未匹配路由规则后的处理
     * @access public
     * @param string $route  路由地址
     * @param string $method 请求类型
     * @param array  $option 路由参数
     * @return void
     */
    public function miss($route, $method = '*', $option = [])
    {
        $this->rule('__miss__', $route, $method, $option, []);
    }

    /**
     * 注册一个自动解析的URL路由
     * @access public
     * @param string $route 路由地址
     * @return void
     */
    public function auto($route)
    {
        $this->rule('__auto__', $route, '*', [], []);
    }

    /**
     * 获取或者批量设置路由定义
     * @access public
     * @param mixed $rules 请求类型或者路由定义数组
     * @return array
     */
    public function rules($rules = '')
    {
        if (is_array($rules)) {
            $this->rules = $rules;
        } elseif ($rules) {
            return true === $rules ? $this->rules : $this->rules[strtolower($rules)];
        } else {
            $rules = $this->rules;
            unset($rules['pattern'], $rules['alias'], $rules['domain'], $rules['name']);
            return $rules;
        }
    }

    /**
     * 检测子域名部署
     * @access public
     * @param Request $request      Request请求对象
     * @param array   $currentRules 当前路由规则
     * @param string  $method       请求类型
     * @return void
     */
    public function checkDomain($request, &$currentRules, $method = 'get')
    {
        // 域名规则
        $rules = $this->rules['domain'];
        // 开启子域名部署 支持二级和三级域名
        if (!empty($rules)) {
            $host = $request->host(true);
            if (isset($rules[$host])) {
                // 完整域名或者IP配置
                $item = $rules[$host];
            } else {
               // 获取当前子域名
               $this->subDomain = $subDomain = $request->subDomain();

                $domain  = $subDomain ? explode('.', $subDomain) : [];
                $domain2 = $domain ? array_pop($domain) : '';

                if ($domain) {
                    // 存在三级域名
                    $domain3 = array_pop($domain);
                }
                
                    if ($subDomain && isset($rules[$subDomain])) {
                        // 子域名配置
                        $item = $rules[$subDomain];
                    } elseif (isset($rules['*.' . $domain2]) && !empty($domain3)) {
                        // 泛三级域名
                        $item      = $rules['*.' . $domain2];
                        $panDomain = $domain3;
                    } elseif (isset($rules['*']) && !empty($domain2)) {
                        // 泛二级域名
                        if ('www' != $domain2) {
                            $item      = $rules['*'];
                            $panDomain = $domain2;
                        }
                    }
                
            }
            if (!empty($item)) {
                if (isset($panDomain)) {
                    // 保存当前泛域名
					$request->setPanDomain($panDomain);
                    //$request->route(['__domain__' => $panDomain]);
                }
                if (isset($item['[bind]'])) {
                    // 解析子域名部署规则
                    list($rule, $option, $pattern) = $item['[bind]'];
                    if (!empty($option['https']) && !$request->isSsl()) {
                        // https检测
                        throw new HttpException(404, 'must use https request:' . $host);
                    }

                    if (strpos($rule, '?')) {
                        // 传入其它参数
                        $array  = parse_url($rule);
                        $result = $array['path'];
                        parse_str($array['query'], $params);
                        if (isset($panDomain)) {
                            $pos = array_search('*', $params);
                            if (false !== $pos) {
                                // 泛域名作为参数
                                $params[$pos] = $panDomain;
                            }
                        }
                        $_GET = array_merge($_GET, $params);
                    } else {
                        $result = $rule;
                    }

                    if (0 === strpos($result, '\\')) {
                        // 绑定到命名空间 例如 \app\index\behavior
                        $this->bind = ['type' => 'namespace', 'namespace' => $result];
                    } elseif (0 === strpos($result, '@')) {
                        // 绑定到类 例如 @app\index\controller\User
                        $this->bind = ['type' => 'class', 'class' => substr($result, 1)];
                    } else {
                        // 绑定到模块/控制器 例如 index/user
                        $this->bind = ['type' => 'module', 'module' => $result];
                    }
                    $this->domainBind = true;
                } else {
                    $this->domainRule = $item;
                    $currentRules     = isset($item[$method]) ? $item[$method] : $item['*'];
                }
            }
        }
    }

    /**
     * 检测URL路由
     * @access public
     * @param Request $request     Request请求对象
     * @param string  $url         URL地址
     * @param string  $depr        URL分隔符
     * @param bool    $checkDomain 是否检测域名规则
     * @return false|array
     */
    public function check($request, $url, $depr = '/', $checkDomain = false)
    {

        // 分隔符替换 确保路由定义使用统一的分隔符
        $url = str_replace($depr, '|', $url);

        if (isset($this->rules['alias'][$url]) || isset($this->rules['alias'][strstr($url, '|', true)])) {
            // 检测路由别名
            $result = $this->checkRouteAlias($request, $url, $depr);
            if (false !== $result) {
                return $result;
            }
        }
        $method = strtolower($request->method());
        // 获取当前请求类型的路由规则
        $rules = isset($this->rules[$method]) ? $this->rules[$method] : [];
        // 检测域名部署
        if ($checkDomain) {
            $this->checkDomain($request, $rules, $method);
        }
        // 检测URL绑定
        $return = $this->checkUrlBind($url, $rules, $depr);
        if (false !== $return) {
            return $return;
        }
        if ('|' != $url) {
            $url = rtrim($url, '|');
        }
        $item = str_replace('|', '/', $url);
        if (isset($rules[$item])) {
            // 静态路由规则检测
            $rule = $rules[$item];
            if (true === $rule) {
                $rule = $this->getRouteExpress($item);
            }
            if (!empty($rule['route']) && $this->checkOption($rule['option'], $request)) {
                $this->setOption($rule['option']);
                return $this->parseRule($request, $item, $rule['route'], $url, $rule['option']);
            }
        }

        // 路由规则检测
        if (!empty($rules)) {
            return $this->checkRoute($request, $rules, $url, $depr);
        }
        return false;
    }

    private function getRouteExpress($key)
    {
        return $this->domainRule ? $this->domainRule['*'][$key] : $this->rules['*'][$key];
    }

    /**
     * 检测路由规则
     * @access private
     * @param Request $request
     * @param array   $rules   路由规则
     * @param string  $url     URL地址
     * @param string  $depr    URL分割符
     * @param string  $group   路由分组名
     * @param array   $options 路由参数（分组）
     * @return mixed
     */
    private function checkRoute($request, $rules, $url, $depr = '/', $group = '', $options = [])
    {
        foreach ($rules as $key => $item) {
            if (true === $item) {
                $item = $this->getRouteExpress($key);
            }
            if (!isset($item['rule'])) {
                continue;
            }
            $rule    = $item['rule'];
            $route   = $item['route'];
            $vars    = $item['var'];
            $option  = $item['option'];
            $pattern = $item['pattern'];

            // 检查参数有效性
            if (!$this->checkOption($option, $request)) {
                continue;
            }

            if (isset($option['ext'])) {
                // 路由ext参数 优先于系统配置的URL伪静态后缀参数
                $url = preg_replace('/\.' . $request->ext() . '$/i', '', $url);
            }

            if (is_array($rule)) {
                // 分组路由
                $pos = strpos(str_replace('<', ':', $key), ':');
                if (false !== $pos) {
                    $str = substr($key, 0, $pos);
                } else {
                    $str = $key;
                }
                if (is_string($str) && $str && 0 !== stripos(str_replace('|', '/', $url), $str)) {
                    continue;
                }
                $this->setOption($option);
                $result = $this->checkRoute($request, $rule, $url, $depr, $key, $option);
                if (false !== $result) {
                    return $result;
                }
            } elseif ($route) {
                if ('__miss__' == $rule || '__auto__' == $rule) {
                    // 指定特殊路由
                    $var    = trim($rule, '__');
                    ${$var} = $item;
                    continue;
                }
                if ($group) {
                    $rule = $group . ($rule ? '/' . ltrim($rule, '/') : '');
                }

                $this->setOption($option);
                if (isset($options['bind_model']) && isset($option['bind_model'])) {
                    $option['bind_model'] = array_merge($options['bind_model'], $option['bind_model']);
                }
                $result = $this->checkRule($request, $rule, $route, $url, $pattern, $option, $depr);
                if (false !== $result) {
                    return $result;
                }
            }
        }
        if (isset($auto)) {
            // 自动解析URL地址
            return $this->parseUrl($auto['route'] . '/' . $url, $depr);
        } elseif (isset($miss)) {
            // 未匹配所有路由的路由规则处理
            return $this->parseRule($request, '', $miss['route'], $url, $miss['option']);
        }
        return false;
    }

    /**
     * 检测路由别名
     * @access private
     * @param Request $request
     * @param string  $url  URL地址
     * @param string  $depr URL分隔符
     * @return mixed
     */
    private function checkRouteAlias($request, $url, $depr)
    {
        $array = explode('|', $url);
        $alias = array_shift($array);
        $item  = $this->rules['alias'][$alias];

        if (is_array($item)) {
            list($rule, $option) = $item;
            $action              = $array[0];
            if (isset($option['allow']) && !in_array($action, explode(',', $option['allow']))) {
                // 允许操作
                return false;
            } elseif (isset($option['except']) && in_array($action, explode(',', $option['except']))) {
                // 排除操作
                return false;
            }
            if (isset($option['method'][$action])) {
                $option['method'] = $option['method'][$action];
            }
        } else {
            $rule = $item;
        }
        $bind = implode('|', $array);
        // 参数有效性检查
        if (isset($option) && !$this->checkOption($option, $request)) {
            // 路由不匹配
            return false;
        } elseif (0 === strpos($rule, '\\')) {
            // 路由到类
            return $this->bindToClass($bind, substr($rule, 1), $depr);
        } elseif (0 === strpos($rule, '@')) {
            // 路由到控制器类
            return $this->bindToController($bind, substr($rule, 1), $depr);
        } else {
            // 路由到模块/控制器
            return $this->bindToModule($bind, $rule, $depr);
        }
    }

    /**
     * 检测URL绑定
     * @access private
     * @param string $url   URL地址
     * @param array  $rules 路由规则
     * @param string $depr  URL分隔符
     * @return mixed
     */
    private function checkUrlBind(&$url, &$rules, $depr = '/')
    {
        if (!empty($this->bind)) {
            $type = $this->bind['type'];
            $bind = $this->bind[$type];
            
            // 如果有URL绑定 则进行绑定检测
            switch ($type) {
                case 'class':
                    // 绑定到类
                    return $this->bindToClass($url, $bind, $depr);
                case 'controller':
                    // 绑定到控制器类
                    return $this->bindToController($url, $bind, $depr);
                case 'namespace':
                    // 绑定到命名空间
                    return $this->bindToNamespace($url, $bind, $depr);
            }
        }
        return false;
    }

    /**
     * 绑定到类
     * @access public
     * @param string $url   URL地址
     * @param string $class 类名（带命名空间）
     * @param string $depr  URL分隔符
     * @return array
     */
    public function bindToClass($url, $class, $depr = '/')
    {
        $url    = str_replace($depr, '|', $url);
        $array  = explode('|', $url, 2);
        $action = !empty($array[0]) ? $array[0] : $this->config('default_action');
        if (!empty($array[1])) {
            $this->parseUrlParams($array[1]);
        }
        return ['type' => 'method', 'method' => [$class, $action], 'var' => []];
    }

    /**
     * 绑定到命名空间
     * @access public
     * @param string $url       URL地址
     * @param string $namespace 命名空间
     * @param string $depr      URL分隔符
     * @return array
     */
    public function bindToNamespace($url, $namespace, $depr = '/')
    {
        $url    = str_replace($depr, '|', $url);
        $array  = explode('|', $url, 3);
        $class  = !empty($array[0]) ? $array[0] : $this->config('default_controller');
        $method = !empty($array[1]) ? $array[1] : $this->config('default_action');
        if (!empty($array[2])) {
            $this->parseUrlParams($array[2]);
        }
        return ['type' => 'method', 'method' => [$namespace . '\\' . $this->parseName($class, 1), $method], 'var' => []];
    }

    /**
     * 绑定到控制器类
     * @access public
     * @param string $url        URL地址
     * @param string $controller 控制器名 （支持带模块名 index/user ）
     * @param string $depr       URL分隔符
     * @return array
     */
    public function bindToController($url, $controller, $depr = '/')
    {
        $url    = str_replace($depr, '|', $url);
        $array  = explode('|', $url, 2);
        $action = !empty($array[0]) ? $array[0] : $this->config('default_action');
        if (!empty($array[1])) {
            $this->parseUrlParams($array[1]);
        }
        return ['type' => 'controller', 'controller' => $controller . '/' . $action, 'var' => []];
    }

    /**
     * 绑定到模块/控制器
     * @access public
     * @param string $url        URL地址
     * @param string $controller 控制器类名（带命名空间）
     * @param string $depr       URL分隔符
     * @return array
     */
    public function bindToModule($url, $controller, $depr = '/')
    {
        $url    = str_replace($depr, '|', $url);
        $array  = explode('|', $url, 2);
        $action = !empty($array[0]) ? $array[0] : $this->config('default_action');
        if (!empty($array[1])) {
            $this->parseUrlParams($array[1]);
        }
        return ['type' => 'module', 'module' => $controller . '/' . $action];
    }

    /**
     * 路由参数有效性检查
     * @access private
     * @param array   $option  路由参数
     * @param Request $request Request对象
     * @return bool
     */
    private function checkOption($option, $request)
    {
		// 请求类型检测
        if (!empty($option['method'])) {
            if (is_string($option['method']) && false === stripos($option['method'], $request->method())) {
                return false;
            }
        }

        // AJAX PJAX 请求检查
        foreach (['ajax', 'pjax', 'json'] as $item) {
            if (isset($option[$item])) {
                $call = 'is' . $item;
                if ($option[$item] && !$request->$call() || !$option[$item] && $request->$call()) {
                    return false;
                }
            }
        }

        // 伪静态后缀检测
        if ($request->url() != '/' && ((isset($option['ext']) && false === stripos('|' . $option['ext'] . '|', '|' . $request->ext() . '|'))
            || (isset($option['deny_ext']) && false !== stripos('|' . $option['deny_ext'] . '|', '|' . $request->ext() . '|')))) {
            return false;
        }

        // 域名检查
        if ((isset($option['domain']) && !in_array($option['domain'], [$request->host(true), $request->subDomain()]))) {
            return false;
        }

        // HTTPS检查
        if ((isset($option['https']) && $option['https'] && !$request->isSsl())
            || (isset($option['https']) && !$option['https'] && $request->isSsl())) {
            return false;
        }

        // 请求参数检查
        if (isset($option['filter'])) {
            foreach ($option['filter'] as $name => $value) {
                if ($request->param($name, '', null) != $value) {
                    return false;
                }
            }
        }
		
		// 自定义检测
		if (!empty($option['callback']) && is_callable($option['callback']) && false === call_user_func($option['callback'])) {
             return false;
        }
        
        return true;
    }

    /**
     * 检测路由规则
     * @access private
     * @param string $rule    路由规则
     * @param string $route   路由地址
     * @param string $url     URL地址
     * @param array  $pattern 变量规则
     * @param array  $option  路由参数
     * @param string $depr    URL分隔符（全局）
     * @return array|false
     */
    private function checkRule($request, $rule, $route, $url, $pattern, $option, $depr)
    {
        // 检查完整规则定义
        if (isset($pattern['__url__']) && !preg_match(0 === strpos($pattern['__url__'], '/') ? $pattern['__url__'] : '/^' . $pattern['__url__'] . '/', str_replace('|', $depr, $url))) {
            return false;
        }
        // 检查路由的参数分隔符
        if (isset($option['param_depr'])) {
            $url = str_replace(['|', $option['param_depr']], [$depr, '|'], $url);
        }

        $len1 = substr_count($url, '|');
        $len2 = substr_count($rule, '/');
        // 多余参数是否合并
        $merge = !empty($option['merge_extra_vars']);
        if ($merge && $len1 > $len2) {
            $url = str_replace('|', $depr, $url);
            $url = implode('|', explode($depr, $url, $len2 + 1));
        }

        if ($len1 >= $len2 || strpos($rule, '[')) {
            if (!empty($option['complete_match'])) {
                // 完整匹配
                if (!$merge && $len1 != $len2 && (false === strpos($rule, '[') || $len1 > $len2 || $len1 < $len2 - substr_count($rule, '['))) {
                    return false;
                }
            }
            $pattern = array_merge($this->rules['pattern'], $pattern);
            if (false !== $match = $this->match($url, $rule, $pattern)) {
                // 匹配到路由规则
                return $this->parseRule($request, $rule, $route, $url, $option, $match);
            }
        }
        return false;
    }

    /**
     * 解析模块的URL地址 [模块/控制器/操作?]参数1=值1&参数2=值2...
     * @access public
     * @param string $url        URL地址
     * @param string $depr       URL分隔符
     * @param bool   $autoSearch 是否自动深度搜索控制器
     * @return array
     */
    public function parseUrl($url, $depr = '/', $autoSearch = false)
    {

        if (isset($this->bind['module'])) {
            $bind = str_replace('/', $depr, $this->bind['module']);
            // 如果有模块/控制器绑定
            $url = $bind . ('.' != substr($bind, -1) ? $depr : '') . ltrim($url, $depr);
        }
        $url              = str_replace($depr, '|', $url);
        list($path, $var) = $this->parseUrlPath($url);
        $route            = [null, null, null];
        if (isset($path)) {
            // 解析模块
            $module = $this->app->config->get('app.multi_app', true) ? array_shift($path) : null;
            if ($autoSearch) {
                // 自动搜索控制器
                $dir    = $this->app->getBasePath() . ($module ? $module . DIRECTORY_SEPARATOR : '') . $this->config('url_controller_layer');
                $suffix = $this->config('controller_suffix') ? ucfirst($this->config('url_controller_layer')) : '';
                $item   = [];
                $find   = false;
                foreach ($path as $val) {
                    $item[] = $val;
                    $file   = $dir . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $val) . $suffix . '.php';
                    $file   = pathinfo($file, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . $this->parseName(pathinfo($file, PATHINFO_FILENAME), 1) . '.php';
                    if (is_file($file)) {
                        $find = true;
                        break;
                    } else {
                        $dir .= DIRECTORY_SEPARATOR . $this->parseName($val);
                    }
                }
                if ($find) {
                    $controller = implode('.', $item);
                    $path       = array_slice($path, count($item));
                } else {
                    $controller = array_shift($path);
                }
            } else {
                // 解析控制器
                $controller = !empty($path) ? array_shift($path) : null;
            }
            // 解析操作
            $action = !empty($path) ? array_shift($path) : null;
            // 解析额外参数
            $this->parseUrlParams(empty($path) ? '' : implode('|', $path));
            // 封装路由
            $route = [$module, $controller, $action];
            // 检查地址是否被定义过路由
            $name  = strtolower($module . '/' . $this->parseName($controller, 1) . '/' . $action);
            $name2 = '';
            if (empty($module) || isset($bind) && $module == $bind) {
                $name2 = strtolower($this->parseName($controller, 1) . '/' . $action);
            }

            if (isset($this->rules['name'][$name]) || isset($this->rules['name'][$name2])) {
                throw new HttpException(404, 'invalid request:' . str_replace('|', $depr, $url));
            }
        }
        return ['type' => 'module', 'module' => $route];
    }

    /**
     * 解析URL的pathinfo参数和变量
     * @access private
     * @param string $url URL地址
     * @return array
     */
    private function parseUrlPath($url)
    {
        // 分隔符替换 确保路由定义使用统一的分隔符
        $url = str_replace('|', '/', $url);
        $url = trim($url, '/');
        $var = [];
        if (false !== strpos($url, '?')) {
            // [模块/控制器/操作?]参数1=值1&参数2=值2...
            $info = parse_url($url);
            $path = explode('/', $info['path']);
            parse_str($info['query'], $var);
        } elseif (strpos($url, '/')) {
            // [模块/控制器/操作]
            $path = explode('/', $url);
        } else {
            $path = [$url];
        }
        return [$path, $var];
    }

    /**
     * 检测URL和规则路由是否匹配
     * @access private
     * @param string $url     URL地址
     * @param string $rule    路由规则
     * @param array  $pattern 变量规则
     * @return array|false
     */
    private function match($url, $rule, $pattern)
    {
        $m2 = explode('/', $rule);
        $m1 = explode('|', $url);

        $var = [];
        foreach ($m2 as $key => $val) {
            // val中定义了多个变量 <id><name>
            if (false !== strpos($val, '<') && preg_match_all('/<(\w+(\??))>/', $val, $matches)) {
                $value   = [];
                $replace = [];
                foreach ($matches[1] as $name) {
                    if (strpos($name, '?')) {
                        $name      = substr($name, 0, -1);
                        $replace[] = '(' . (isset($pattern[$name]) ? $pattern[$name] : '\w+') . ')?';
                    } else {
                        $replace[] = '(' . (isset($pattern[$name]) ? $pattern[$name] : '\w+') . ')';
                    }
                    $value[] = $name;
                }
                $val = str_replace($matches[0], $replace, $val);
                if (preg_match('/^' . $val . '$/', isset($m1[$key]) ? $m1[$key] : '', $match)) {
                    array_shift($match);
                    foreach ($value as $k => $name) {
                        if (isset($match[$k])) {
                            $var[$name] = $match[$k];
                        }
                    }
                    continue;
                } else {
                    return false;
                }
            }

            if (0 === strpos($val, '[:')) {
                // 可选参数
                $val      = substr($val, 1, -1);
                $optional = true;
            } else {
                $optional = false;
            }
            if (0 === strpos($val, ':')) {
                // URL变量
                $name = substr($val, 1);
                if (!$optional && !isset($m1[$key])) {
                    return false;
                }
                if (isset($m1[$key]) && isset($pattern[$name])) {
                    // 检查变量规则
                    if ($pattern[$name] instanceof \Closure) {
                        $result = call_user_func_array($pattern[$name], [$m1[$key]]);
                        if (false === $result) {
                            return false;
                        }
                    } elseif (!preg_match(0 === strpos($pattern[$name], '/') ? $pattern[$name] : '/^' . $pattern[$name] . '$/', $m1[$key])) {
                        return false;
                    }
                }
                $var[$name] = isset($m1[$key]) ? $m1[$key] : '';
            } elseif (!isset($m1[$key]) || 0 !== strcasecmp($val, $m1[$key])) {
                return false;
            }
        }
        // 成功匹配后返回URL中的动态变量数组
        return $var;
    }

    /**
     * 解析规则路由
     * @access private
     * @param string $rule      路由规则
     * @param string $route     路由地址
     * @param string $pathinfo  URL地址
     * @param array  $option    路由参数
     * @param array  $matches   匹配的变量
     * @return array
     */
    private function parseRule($request, $rule, $route, $pathinfo, $option = [], $matches = [])
    {
      

        // 解析路由规则
        if ($rule) {
            $rule = explode('/', $rule);
            // 获取URL地址中的参数
            $paths = explode('|', $pathinfo);
            foreach ($rule as $item) {
                $fun = '';
                if (0 === strpos($item, '[:')) {
                    $item = substr($item, 1, -1);
                }
                if (0 === strpos($item, ':')) {
                    $var           = substr($item, 1);
                    $matches[$var] = array_shift($paths);
                } else {
                    // 过滤URL中的静态变量
                    array_shift($paths);
                }
            }
        } else {
            $paths = explode('|', $pathinfo);
        }

        // 获取路由地址规则
        if (is_string($route) && isset($option['prefix'])) {
            // 路由地址前缀
            $route = $option['prefix'] . $route;
        }
        // 替换路由地址中的变量
        if (is_string($route) && !empty($matches)) {
            foreach ($matches as $key => $val) {
                if (false !== strpos($route, ':' . $key)) {
                    $route = str_replace(':' . $key, $val, $route);
                }
            }
        }

        // 绑定模型数据
        if (isset($option['bind_model'])) {
            $this->option['model'] = $option['bind_model'];
        }

        

        // 解析额外参数
        $this->parseUrlParams(empty($paths) ? '' : implode('|', $paths), $matches);
        // 记录匹配的路由信息
        $request->routeInfo(['rule' => $rule, 'route' => $route, 'option' => $option, 'var' => $matches]);

        if ($route instanceof \Closure) {
            // 执行闭包
            $result = ['type' => 'function', 'function' => $route];
        } elseif (0 === strpos($route, '/') || strpos($route, '://')) {
            // 路由到重定向地址
            $result = ['type' => 'redirect', 'url' => $route, 'status' => isset($option['status']) ? $option['status'] : 301];
        } elseif (false !== strpos($route, '\\')) {
            // 路由到方法
            list($path, $var) = $this->parseUrlPath($route);
            $route            = str_replace('/', '@', implode('/', $path));
            $method           = strpos($route, '@') ? explode('@', $route) : $route;
            $result           = ['type' => 'method', 'method' => $method, 'var' => $var];
        } elseif (0 === strpos($route, '@')) {
            // 路由到控制器
            $route             = substr($route, 1);
            list($route, $var) = $this->parseUrlPath($route);
            $result            = ['type' => 'controller', 'controller' => implode('/', $route), 'var' => $var];
            $request->setAction(array_pop($route));
            $request->setController($route ? array_pop($route) : $this->config('default_controller'));
            $this->app->http->name($route ? array_pop($route) : $this->config('default_module'));
            $appPath = $this->app->getBasePath() . ($this->app->config->get('app.multi_app', true) ? $this->app->http->getName() . DIRECTORY_SEPARATOR : '');
			$this->app->setAppPath($appPath);
        } else {
            // 路由到模块/控制器/操作
            $result = $this->parseModule($request, $route, isset($option['convert']) ? $option['convert'] : false);
        }
        // 开启请求缓存
        if ($request->isGet() && isset($option['cache'])) {
            $this->cache($option['cache']);
        }
        return $result;
    }

    /**
     * 解析URL地址为 模块/控制器/操作
     * @access private
     * @param string $url     URL地址
     * @param bool   $convert 是否自动转换URL地址
     * @return array
     */
    private function parseModule($request, $url, $convert = false)
    {
        list($path, $var) = $this->parseUrlPath($url);
        $action           = array_pop($path);
        $controller       = !empty($path) ? array_pop($path) : null;
        $module           = $this->app->config->get('app.multi_app', true) && !empty($path) ? array_pop($path) : null;
        $method           = $request->method();
        if ($this->config('use_action_prefix') && !empty($this->methodPrefix[$method])) {
            // 操作方法前缀支持
            $action = 0 !== strpos($action, $this->methodPrefix[$method]) ? $this->methodPrefix[$method] . $action : $action;
        }
        // 设置当前请求的路由变量
		$this->setVars($var);
       
        // 路由到模块/控制器/操作
        return ['type' => 'module', 'module' => [$module, $controller, $action], 'convert' => $convert];
    }

    /**
     * 解析URL地址中的参数Request对象
     * @access private
     * @param string $url 路由规则
     * @param array  $var 变量
     * @return void
     */
    private function parseUrlParams($url, &$var = [])
    {
        if ($url) {
            if ($this->config('url_param_type')) {
                $var += explode('|', $url);
            } else {
                preg_replace_callback('/(\w+)\|([^\|]+)/', function ($match) use (&$var) {
                    $var[$match[1]] = strip_tags($match[2]);
                }, $url);
            }
        }
        // 设置当前请求的参数
        $this->setVars($var);
    }

    // 分析路由规则中的变量
    private function parseVar($rule)
    {
        // 提取路由规则中的变量
        $var = [];
        foreach (explode('/', $rule) as $val) {
            $optional = false;
            if (false !== strpos($val, '<') && preg_match_all('/<(\w+(\??))>/', $val, $matches)) {
                foreach ($matches[1] as $name) {
                    if (strpos($name, '?')) {
                        $name     = substr($name, 0, -1);
                        $optional = true;
                    } else {
                        $optional = false;
                    }
                    $var[$name] = $optional ? 2 : 1;
                }
            }

            if (0 === strpos($val, '[:')) {
                // 可选参数
                $optional = true;
                $val      = substr($val, 1, -1);
            }
            if (0 === strpos($val, ':')) {
                // URL变量
                $name       = substr($val, 1);
                $var[$name] = $optional ? 2 : 1;
            }
        }
        return $var;
    }
	/***************************/
	/**
     * 需要和分组合并的路由参数
     * @var array
     */
    protected $mergeOptions = ['model', 'append', 'middleware'];
	
	/**
     * 设置需要合并的路由参数
     * @access public
     * @param  array $option 路由参数
     * @return $this
     */
    public function mergeOptions(array $option = [])
    {
        $this->mergeOptions = array_merge($this->mergeOptions, $option);
        return $this;
    }
	/**
     * 绑定模型
     * @access public
     * @param  array|string|Closure $var  路由变量名 多个使用 & 分割
     * @param  string|Closure       $model 绑定模型类
     * @param  bool                  $exception 是否抛出异常
     * @return $this
     */
    public function model($var, $model = null, bool $exception = true)
    {
        if ($var instanceof Closure) {
            $this->option['model'][] = $var;
        } elseif (is_array($var)) {
            $this->option['model'] = $var;
        } elseif (is_null($model)) {
            $this->option['model']['id'] = [$var, true];
        } else {
            $this->option['model'][$var] = [$model, $exception];
        }

        return $this;
    }

    /**
     * 附加路由隐式参数
     * @access public
     * @param  array $append 追加参数
     * @return $this
     */
    public function append(array $append = [])
    {
        $this->option['append'] = $append;

        return $this;
    }

    /**
     * 绑定验证
     * @access public
     * @param  mixed  $validate 验证器类
     * @param  string $scene 验证场景
     * @param  array  $message 验证提示
     * @param  bool   $batch 批量验证
     * @return $this
     */
    public function validate($validate, string $scene = null, array $message = [], bool $batch = false)
    {
        $this->option['validate'] = [$validate, $scene, $message, $batch];

        return $this;
    }

    /**
     * 指定路由中间件
     * @access public
     * @param string|array|Closure $middleware 中间件
     * @param mixed $params 参数
     * @return $this
     */
    public function middleware($middleware, ...$params)
    {
        if (empty($params) && is_array($middleware)) {
            $this->option['middleware'] = $middleware;
        } else {
            foreach ((array) $middleware as $item) {
                $this->option['middleware'][] = [$item, $params];
            }
        }

        return $this;
    }

    /**
     * 允许跨域
     * @access public
     * @param  array $header 自定义Header
     * @return $this
     */
    public function allowCrossDomain(array $header = [])
    {
        return $this->middleware(AllowCrossDomain::class, $header);
    }

    /**
     * 表单令牌验证
     * @access public
     * @param  string $token 表单令牌token名称
     * @return $this
     */
    public function token(string $token = '__token__')
    {
        return $this->middleware(FormTokenCheck::class, $token);
    }

    /**
     * 设置路由缓存
     * @access public
     * @param  array|string $cache 缓存
     * @return $this
     */
    public function cache($cache)
    {
        return $this->middleware(CheckRequestCache::class, $cache);
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
    public function parseName($name, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);

            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
	
	/**
     * URL生成 支持路由反射
     * @access public
     * @param string $url  路由地址
     * @param array  $vars 参数 ['a'=>'val1', 'b'=>'val2']
     * @return UrlBuild
     */
    public function buildUrl(string $url = '', array $vars = []): UrlBuild
    {
        return $this->app->make(UrlBuild::class, [$this, $this->app, $url, $vars], true);
    }
	
}
