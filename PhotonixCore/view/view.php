<?php

namespace PhotonixCore\View;

use PhotonixCore\Photonix;

class View
{
    protected static array $config = [];
    protected static array $sharedVars = [];

    protected static function loadConfig(): array
    {
        if (!self::$config) {
            $root = Photonix::getAppRootPath();
            $file = $root . 'config/view.php';
            self::$config = is_file($file) ? (array)require $file : [];
        }
        return self::$config;
    }

    protected static function ensureCachedDir(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
    }

    protected static function compileTemplate(string $source, array $cfg): string
    {
        $varL = $cfg['view_display_l'] ?? '{{ ';
        $varR = $cfg['view_display_r'] ?? ' }}';
        $codeL = $cfg['view_code_display_l'] ?? '{% ';
        $codeR = $cfg['view_code_display_r'] ?? ' %} ';
        $s = $source;
        $s = preg_replace('/' . preg_quote($codeL, '/') . '(.*?)' . preg_quote($codeR, '/') . '/s', '<?php $1 ?>', $s);
        $s = preg_replace('/' . preg_quote($varL, '/') . '(.*?)' . preg_quote($varR, '/') . '/s', '<?= $1 ?>', $s);
        $s = preg_replace_callback('/\{\$([A-Za-z_][A-Za-z0-9_\.\[\]]*)(?:\|([^}]+))?\}/', function ($m) {
            $expr = View::toPhpVar($m[1]);
            if (!empty($m[2])) {
                $expr = View::applyModifiers($expr, $m[2]);
            }
            return '<?= ' . $expr . ' ?>';
        }, $s);
        $s = preg_replace('/\{if\s+(.*?)\}/s', '<?php if ($1) { ?>', $s);
        $s = preg_replace('/\{elseif\s+(.*?)\}/s', '<?php } elseif ($1) { ?>', $s);
        $s = preg_replace('/\{else\}/s', '<?php } else { ?>', $s);
        $s = preg_replace('/\{\/if\}/s', '<?php } ?>', $s);
        $s = preg_replace_callback('/\{foreach\s+from=(\$[A-Za-z_][A-Za-z0-9_\.\[\]]*)\s+item=([A-Za-z_][A-Za-z0-9_]*)\s*(key=([A-Za-z_][A-Za-z0-9_]*))?\}/', function ($m) {
            $from = View::toPhpVar(substr($m[1], 1));
            $item = '$' . $m[2];
            if (!empty($m[4])) {
                $key = '$' . $m[4];
                return '<?php foreach (' . $from . ' as ' . $key . ' => ' . $item . ') { ?>';
            }
            return '<?php foreach (' . $from . ' as ' . $item . ') { ?>';
        }, $s);
        $s = preg_replace('/\{\/foreach\}/s', '<?php } ?>', $s);
        $s = preg_replace_callback('/\{include\s+file=([\'\"])(.*?)\1\}/', function ($m) {
            $file = $m[2];
            return '<?php echo \\PhotonixCore\\View\\View::display(' . var_export($file, true) . ', \"cached\", get_defined_vars()); ?>';
        }, $s);
        $s = preg_replace_callback('/\{assign\s+var=([\'\"])(.*?)\1\s+value=([^}]+)\}/', function ($m) {
            $lhs = View::toPhpVar($m[2]);
            $raw = trim($m[3]);
            if (preg_match('/^([\'\"])(.*)\1$/', $raw, $mm)) {
                $rhs = var_export($mm[2], true);
            } elseif (strpos($raw, '$') === 0) {
                $rhs = View::toPhpVar(substr($raw, 1));
            } else {
                $rhs = $raw;
            }
            return '<?php ' . $lhs . ' = ' . $rhs . '; ?>';
        }, $s);
        return $s;
    }

    protected static function toPhpVar(string $name): string
    {
        $parts = preg_split('/\.|\[|\]/', $name, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return '$' . $name;
        }
        $base = '$' . array_shift($parts);
        $expr = $base;
        foreach ($parts as $p) {
            if (ctype_digit($p)) {
                $expr .= '[' . $p . ']';
            } else {
                $expr .= '[' . var_export($p, true) . ']';
            }
        }
        return $expr;
    }

    protected static function applyModifiers(string $expr, string $mods): string
    {
        $tokens = explode('|', $mods);
        $out = $expr;
        foreach ($tokens as $t) {
            $t = trim($t);
            if ($t === 'escape') {
                $out = 'htmlspecialchars(' . $out . ', ENT_QUOTES, \"UTF-8\")';
            } elseif ($t === 'lower') {
                $out = 'strtolower(' . $out . ')';
            } elseif ($t === 'upper') {
                $out = 'strtoupper(' . $out . ')';
            } elseif (strpos($t, 'default:') === 0) {
                $val = trim(substr($t, 8));
                $out = '(' . $out . ' !== \"\" ? ' . $out . ' : ' . $val . ')';
            }
        }
        return $out;
    }

    public static function display(string $tpl_path, $type = 'cached', array $vars = []): string
    {
        $cfg = self::loadConfig();
        $root = Photonix::getAppRootPath();
        $suffix = $cfg['view_suffix'] ?? '.tpl';
        $isAbs = (substr($tpl_path, 0, 1) === '/' || substr($tpl_path, 0, 1) === '\\');
        $rel = ltrim($tpl_path, "\\/");
        $tpl = $root . ($isAbs ? $rel : ('views/' . $rel));
        if (substr($tpl, -strlen($suffix)) !== $suffix) {
            $tpl .= $suffix;
        }
        $cachedDir = $root . 'runtime/cached';
        self::ensureCachedDir($cachedDir);
        $cacheFile = $cachedDir . '/' . md5($tpl) . '.php';
        $useCached = (bool)($cfg['view_code_use_cached'] ?? true);
        if (!$useCached || !is_file($cacheFile) || (is_file($tpl) && filemtime($tpl) > filemtime($cacheFile))) {
            $src = is_file($tpl) ? file_get_contents($tpl) : '';
            $compiled = self::compileTemplate($src, $cfg);
            file_put_contents($cacheFile, $compiled);
        }
        ob_start();
        $scope = self::$sharedVars;
        foreach ($vars as $k => $v) {
            $scope[$k] = $v;
        }
        if (!empty($scope)) {
            extract($scope, EXTR_OVERWRITE);
        }
        include $cacheFile;
        return ob_get_clean();
    }

    public static function assign(string $var, $value): void
    {
        self::setVarByPath(self::$sharedVars, $var, $value);
    }

    protected static function setVarByPath(array &$target, string $path, $value): void
    {
        $parts = preg_split('/\.|\[|\]/', $path, -1, PREG_SPLIT_NO_EMPTY);
        if (!$parts) {
            return;
        }
        $ref =& $target;
        $count = count($parts);
        for ($i = 0; $i < $count; $i++) {
            $p = $parts[$i];
            $last = ($i === $count - 1);
            if ($last) {
                $ref[$p] = $value;
            } else {
                if (!isset($ref[$p]) || !is_array($ref[$p])) {
                    $ref[$p] = [];
                }
                $ref =& $ref[$p];
            }
        }
    }
}