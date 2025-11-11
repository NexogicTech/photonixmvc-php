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

// 检测PHP环境
if (version_compare(PHP_VERSION, '8.0.0', '<')) {
    die('require PHP > 8.0.0 !');
}

require_once __DIR__ . '/../vendor/autoload.php';

use PhotonixCore\Routes;

// 执行路由调度
Routes::runRouter();

