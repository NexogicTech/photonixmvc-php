<?php

// 统一路由：处理静态资源与应用前端控制器
// - 在 PHP 内置服务器 (cli-server) 下，返回 false 交由其直接服务静态文件
// - 其他环境中，手动输出静态文件；否则回退到前端控制器处理动态路由

$__renderError = function () {
    $file = __DIR__ . '/../config/error.php';
    $data = is_file($file) ? require $file : [];
    $html = is_array($data) && isset($data['error']) ? $data['error'] : '<center><h1>服务器发生错误</h1></center>';
    http_response_code(500);
    echo $html;
};

require_once __DIR__ . '/../vendor/autoload.php';

use PhotonixCore\Logger;

ini_set('display_errors', '0');
error_reporting(E_ALL);

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

$docRoot = $_SERVER['DOCUMENT_ROOT'] ?? __DIR__;
$docRootReal = realpath($docRoot) ?: $docRoot;
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$path = parse_url($uri, PHP_URL_PATH) ?: '/';
$path = urldecode($path);

// Resolve requested file path safely
$requested = $docRootReal . $path;
$resolved = realpath($requested);
if ($resolved !== false && strpos($resolved, $docRootReal) === 0 && is_file($resolved)) {
    $ext = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
    if ($ext !== 'php') {
        if (PHP_SAPI === 'cli-server') {
            return false;
        }
        $mime = 'application/octet-stream';
        if (function_exists('mime_content_type')) {
            $detected = mime_content_type($resolved);
            if (is_string($detected) && $detected !== '') {
                $mime = $detected;
            }
        } else if (function_exists('finfo_open')) {
            $fi = finfo_open(FILEINFO_MIME_TYPE);
            if ($fi) {
                $detected = finfo_file($fi, $resolved);
                if (is_string($detected) && $detected !== '') {
                    $mime = $detected;
                }
                finfo_close($fi);
            }
        }
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($resolved));
        readfile($resolved);
        exit;
    }
}

// Favicon 兜底：若缺少 .ico 则尝试返回 png/svg
if ($path === '/favicon.ico') {
    $png = $docRootReal . '/favicon.png';
    $svg = $docRootReal . '/favicon.svg';
    if (is_file($png)) {
        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($png));
        readfile($png);
        exit;
    }
    if (is_file($svg)) {
        header('Content-Type: image/svg+xml');
        readfile($svg);
        exit;
    }
}

// Fallback to front controller for dynamic routes
$_SERVER['SCRIPT_FILENAME'] = $docRootReal . '/index.php';
require $_SERVER['SCRIPT_FILENAME'];