<?php

namespace PhotonixCore;

class Logger
{
    protected static array $cfg = [];

    protected static function cfg(): array
    {
        if (!self::$cfg) {
            $root = Photonix::getAppRootPath();
            $file = $root . 'config/log.php';
            self::$cfg = is_file($file) ? (array)require $file : [];
        }
        return self::$cfg;
    }

    protected static function dir(): string
    {
        $root = Photonix::getAppRootPath();
        $dir = $root . 'runtime/log';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    protected static function levelToInt(string $l): int
    {
        $map = ['debug' => 0, 'info' => 1, 'warn' => 2, 'error' => 3];
        $k = strtolower($l);
        return $map[$k] ?? 0;
    }

    protected static function resolvePattern(string $pattern): string
    {
        return preg_replace_callback('/\{([^}]+)\}/', function ($m) {
            return date($m[1]);
        }, $pattern) ?? $pattern;
    }

    protected static function filePath(string $level): string
    {
        $c = self::cfg();
        $base = self::dir() . '/' . self::resolvePattern($c['filename_pattern'] ?? 'app-{Y-m-d}.log');
        if (!empty($c['separate_by_level'])) {
            $base = preg_replace('/\.log$/', '-' . strtolower($level) . '.log', $base) ?? ($base . '-' . strtolower($level));
        }
        return $base;
    }

    protected static function rotate(string $file): void
    {
        $c = self::cfg();
        $max = (int)($c['max_file_size'] ?? 0);
        if ($max > 0 && is_file($file) && filesize($file) > $max) {
            $dst = $file . '.' . date('YmdHis');
            @rename($file, $dst);
        }
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $c = self::cfg();
        if (empty($c['enabled_log_file'])) {
            return;
        }
        $min = self::levelToInt((string)($c['level'] ?? 'debug'));
        if (self::levelToInt($level) < $min) {
            return;
        }
        $tz = (string)($c['timezone'] ?? (date_default_timezone_get() ?: 'UTC'));
        @date_default_timezone_set($tz);
        $ts = date('Y-m-d H:i:s');
        $fmt = (string)($c['format'] ?? 'line');
        $line = $fmt === 'json'
            ? json_encode(['time' => $ts, 'level' => strtoupper($level), 'message' => $message, 'context' => $context], JSON_UNESCAPED_UNICODE)
            : ($ts . ' [' . strtoupper($level) . '] ' . $message . (empty($context) ? '' : ' ' . json_encode($context, JSON_UNESCAPED_UNICODE)));
        $file = self::filePath($level);
        self::rotate($file);
        $fp = @fopen($file, 'ab');
        if ($fp) {
            @fwrite($fp, $line . PHP_EOL);
            @fclose($fp);
        }
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function warn(string $message, array $context = []): void
    {
        self::log('warn', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }
}