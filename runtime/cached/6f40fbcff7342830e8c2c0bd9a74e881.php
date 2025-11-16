<?php
// +----------------------------------------------------------------------
// | Nexogic Photonix MVC 模板
// +----------------------------------------------------------------------
// | 版权 (c) 2025 http://www.nexogic.org 保留所有权利
// +----------------------------------------------------------------------
// | 许可证 (MIT): https://opensource.org/license/MIT
// +----------------------------------------------------------------------
// | 作者: HelixDev <dev@nexogic.org>
// +----------------------------------------------------------------------

if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('Photonix MVC require PHP > 8.0.0 !');
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
    hello Photonix <?= $version ?>你好!
</body>
</html>