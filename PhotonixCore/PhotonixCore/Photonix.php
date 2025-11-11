<?php
/*
 * Photonix应用类
 */

namespace PhotonixCore;
// 测试用require_once __DIR__ . '/../vendor/autoload.php';

class Photonix
{
    /**
     * @return string 获取Photonix版本
     */
    public static function version(): string
    {
        return env::env("APP_VERSION");
    }

    /**
     * @return string 获取项目根目录
     */
    public static function getAppRootPath(): string
    {
        return dirname(__DIR__) . DIRECTORY_SEPARATOR;
    }
}

// echo Photonix::getAppRootPath();