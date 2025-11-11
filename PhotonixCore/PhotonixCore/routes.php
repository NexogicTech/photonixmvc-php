<?php

namespace PhotonixCore;

require_once __DIR__ . '/response.php';

class Routes
{
    /**
     * 标记应用路由是否已加载
     */
    protected static bool $appRoutesLoaded = false;
    /**
     * 路由表，按请求方法分类
     * 每项包含：
     *  - url: 原始路由字符串
     *  - controller: 控制器 Class/Method
     *  - path: 可选类文件路径（相对 /app）
     *  - is_param: 是否为参数化路由（包含 {name} ）
     *  - regex: 参数路由的正则（含命名分组）
     *  - params: 参数名列表
     * @var array<string, array<int, array{url:string, controller:string, path:string, is_param:bool, regex:string|null, params:array}>>
     */
    protected static array $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
        'OPTIONS' => [],
        'ANY' => [],
    ];

    /**
     * 通用注册
     * @param string $method 请求方法
     * @param string $url 路由
     * @param string $controller 控制器（类/方法）
     * @param string $path 类文件路径（相对/app，如 index.php为 /home/controller/index.php）
     * @return void
     */
    protected static function add(string $method, string $url, string $controller, string $path = ''): void
    {
        $method = strtoupper($method);
        if (!isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }
        $normalized = rtrim($url, '/') ?: '/';

        // 检测并编译参数化路由：/user/{id}
        $isParam = preg_match('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', $normalized) === 1;
        $regex = null;
        $params = [];
        if ($isParam) {
            // 收集参数名
            if (preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $normalized, $m)) {
                $params = $m[1];
            }
            // 将 {name} 替换为命名分组
            $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $normalized);
            $regex = '#^' . $pattern . '$#';
        }

        self::$routes[$method][] = [
            'url' => $normalized,
            'controller' => $controller,
            'path' => $path,
            'is_param' => $isParam,
            'regex' => $regex,
            'params' => $params,
        ];
    }

    /**
     * GET 注册路由
     */
    public static function get(string $url, string $controller, string $path = ''): void
    {
        self::add('GET', $url, $controller, $path);
    }

    /**
     * POST 注册路由
     */
    public static function post(string $url, string $controller, string $path = ''): void
    {
        self::add('POST', $url, $controller, $path);
    }

    /**
     * PUT 注册路由
     */
    public static function put(string $url, string $controller, string $path = ''): void
    {
        self::add('PUT', $url, $controller, $path);
    }

    /**
     * DELETE 注册路由
     */
    public static function delete(string $url, string $controller, string $path = ''): void
    {
        self::add('DELETE', $url, $controller, $path);
    }

    /**
     * PATCH 注册路由
     */
    public static function patch(string $url, string $controller, string $path = ''): void
    {
        self::add('PATCH', $url, $controller, $path);
    }

    /**
     * OPTIONS 注册路由
     */
    public static function options(string $url, string $controller, string $path = ''): void
    {
        self::add('OPTIONS', $url, $controller, $path);
    }

    /**
     * ANY 注册路由（任意方法）
     */
    public static function any(string $url, string $controller, string $path = ''): void
    {
        self::add('ANY', $url, $controller, $path);
    }

    /**
     * 运行路由调度
     */
    public static function runRouter(): void
    {
        // 在调度前确保加载应用的路由注册文件
        self::loadAppRoutesOnce();

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        // 去掉查询串，标准化路径
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $requestPath = parse_url($uri, PHP_URL_PATH) ?: '/';
        $requestPath = rtrim($requestPath, '/') ?: '/';

        // 优先匹配对应方法，再匹配 ANY
        $candidates = array_merge(self::$routes[$method] ?? [], self::$routes['ANY'] ?? []);

        // 1. 精确匹配
        foreach ($candidates as $route) {
            if (!$route['is_param'] && $route['url'] === $requestPath) {
                self::dispatchToController($route['controller'], $route['path'], []);
                return;
            }
        }

        // 2. 参数化匹配
        foreach ($candidates as $route) {
            if ($route['is_param'] && $route['regex'] && preg_match($route['regex'], $requestPath, $matches)) {
                // 取出命名分组作为参数
                $params = [];
                foreach ($route['params'] as $name) {
                    if (isset($matches[$name])) {
                        $params[$name] = $matches[$name];
                    }
                }
                self::dispatchToController($route['controller'], $route['path'], $params);
                return;
            }
        }

        // 未匹配到，返回 404 页面
        http_response_code(404);
        echo self::renderError('404');
    }

    /**
     * 仅加载一次 app 路由注册文件
     */
    protected static function loadAppRoutesOnce(): void
    {
        if (self::$appRoutesLoaded) {
            return;
        }
        $file = dirname(__DIR__) . '/app/routes/app.php';
        if (is_file($file)) {
            require_once $file;
        }
        self::$appRoutesLoaded = true;
    }

    /**
     * 调用控制器
     * @param string $controller 格式：Class/Method
     * @param string $path 相对 /app 的类文件路径（可选）
     * @return void
     */
    protected static function dispatchToController(string $controller, string $path = '', array $params = []): void
    {
        [$className, $methodName] = array_pad(explode('/', $controller, 2), 2, null);

        if (!$className || !$methodName) {
            http_response_code(500);
            echo self::renderError('error');
            return;
        }

        $appRoot = dirname(__DIR__) . '/app';
        if ($path !== '') {
            $file = $appRoot . ((substr($path, 0, 1) === '/') ? $path : ('/' . $path));
            if (is_file($file)) {
                require_once $file;
            }
        }

        if (!class_exists($className)) {
            // 尝试无命名空间类名失败，返回 500 以便调试
            http_response_code(500);
            echo self::renderError('error');
            return;
        }

        $instance = new $className();
        if (!method_exists($instance, $methodName)) {
            http_response_code(500);
            echo self::renderError('error');
            return;
        }

        // 执行控制器方法
        try {
            $ref = new \ReflectionMethod($instance, $methodName);
            $parameters = $ref->getParameters();

            // 0) 无参数：直接调用
            if (count($parameters) === 0) {
                $result = $instance->$methodName();
            } else {
                // 1) 兼容旧风格：单参数且名为 params 或类型为 array，则整体传入
                $first = $parameters[0];
                $firstIsArray = $first->hasType() && (string)$first->getType() === 'array';
                if (count($parameters) === 1 && ($first->getName() === 'params' || $firstIsArray)) {
                    $result = $instance->$methodName($params);
                } else {
                    // 2) 映射命名参数到方法形参：按形参名取 $params 值
                    $orderedArgs = [];
                    foreach ($parameters as $p) {
                        $name = $p->getName();
                        if (array_key_exists($name, $params)) {
                            $orderedArgs[] = $params[$name];
                        } else if ($p->isDefaultValueAvailable()) {
                            $orderedArgs[] = $p->getDefaultValue();
                        } else {
                            // 缺参时使用 null，以避免硬错误（由方法自行处理）
                            $orderedArgs[] = null;
                        }
                    }
                    // 使用参数展开调用（PHP 8+）
                    $result = $instance->$methodName(...$orderedArgs);
                }
            }

            if ($result instanceof HtmlResponse) {
                echo $result->content;
            } else if (is_string($result)) {
                echo $result;
            } else if ($result !== null) {
                http_response_code(500);
                echo self::renderError('error');
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo self::renderError('error');
            return;
        }
    }

    /**
     * 渲染错误页面，从 config/error.php 中读取
     * @param string $code 错误代码键：'404'、'403'、'error'
     * @return string
     */
    protected static function renderError(string $code): string
    {
        $errors = self::getErrors();
        return $errors[$code] ?? '<center><h1>Error</h1></center>';
    }

    /**
     * 读取错误配置
     * @return array
     */
    protected static function getErrors(): array
    {
        $file = dirname(__DIR__) . '/config/error.php';
        if (is_file($file)) {
            $data = require $file;
            if (is_array($data)) {
                return $data;
            }
        }
        return [
            '404' => '<center><h1>404 Not Found</h1></center>',
            '403' => '<center><h1>403 Error</h1></center>',
            'error' => '<center><h1>服务器发生错误</h1></center>',
        ];
    }
}