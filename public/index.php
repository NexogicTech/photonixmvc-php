<?php
// +----------------------------------------------------------------------
// | Photonix MVC
// +----------------------------------------------------------------------
// | Copyright (c) 2025 http://nexogic.org All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( https://opensource.org/license/MIT )
// +----------------------------------------------------------------------
// | Author: helixDev <dev@nexogic.org>
// +----------------------------------------------------------------------

// 应用入口文件

require_once __DIR__ . '/../vendor/autoload.php';

use PhotonixCore\Routes;
use PhotonixCore\Logger;

ini_set('display_errors', '0');
error_reporting(E_ALL);

$__renderError = function () {
    $file = __DIR__ . '/../config/error.php';
    $data = is_file($file) ? require $file : [];
    $html = is_array($data) && isset($data['error']) ? $data['error'] : '<center><h1>服务器发生错误</h1></center>';
    http_response_code(500);
    echo $html;
};

set_error_handler(function ($severity, $message, $file, $line) use ($__renderError) {
    Logger::error($message, ['severity' => $severity, 'file' => $file, 'line' => $line]);
    $__renderError();
    exit;
});

set_exception_handler(function ($e) use ($__renderError) {
    Logger::error($e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
    $__renderError();
    exit;
});

register_shutdown_function(function () use ($__renderError) {
    $e = error_get_last();
    if ($e) {
        Logger::error($e['message'] ?? 'shutdown', ['severity' => $e['type'] ?? null, 'file' => $e['file'] ?? null, 'line' => $e['line'] ?? null]);
        $__renderError();
    }
});

// 执行路由调度
Routes::runRouter();

