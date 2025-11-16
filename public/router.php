<?php

// Unified router for static files and app front controller.
// - If running with PHP built-in server (cli-server), return false to let it serve files.
// - Otherwise stream files manually (e.g., when used behind other setups).

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

// Favicon fallback: serve png/svg if .ico is missing
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