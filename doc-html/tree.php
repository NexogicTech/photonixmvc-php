<?php
// 扫描 docs 目录并输出 JSON 树结构
header('Content-Type: application/json; charset=utf-8');
$base = realpath(__DIR__ . '/../docs');
echo json_encode(scan($base), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

function scan($dir, $prefix = '') {
    $items = [];
    foreach (scandir($dir) as $name) {
        if ($name === '.' || $name === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $name;
        $rel = $prefix ? $prefix . '/' . $name : $name;
        if (is_dir($path)) {
            $items[] = [
                'name' => basename($name),
                'path' => '',
                'children' => scan($path, $rel)
            ];
        } elseif (preg_match('/\.md$/i', $name)) {
            $items[] = [
                'name' => preg_replace('/\.md$/i', '', $name),
                'path' => $rel,
                'children' => []
            ];
        }
    }
    return $items;
}