<?php
namespace PhotonixCore;

class Env
{
    /**
     * 通过键获取环境变量返回
     * @param $key string
     * @param $default string
     * @param $env string
     * @return string 对应key的值
     */
    public static function env(string $key, string $default = "", string $env = "../photonix.env"): string
    {
        static $loaded = false;
        static $envData = [];

        // 仅加载一次.env文件
        if (!$loaded) {
            if (file_exists($env)) {
                $lines = file($env, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    // 跳过注释和非键值对行
                    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                        continue;
                    }
                    list($k, $v) = explode('=', $line, 2);
                    $k = trim($k);
                    $v = trim($v);
                    // 处理引号包裹的值
                    if (preg_match('/^(["\'])(.*)\1$/', $v, $m)) {
                        $v = $m[2];
                    }
                    $envData[$k] = $v;
                }
            }
            $loaded = true;
        }

        // 返回对应值或默认值
        return $envData[$key] ?? (string)$default;
    }
}