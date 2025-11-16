<?php

namespace PhotonixCore;

class Request
{
    protected string $method;
    protected array $query;
    protected array $post;
    protected array $cookies;
    protected array $files;
    protected array $headers;
    protected string $body;
    protected ?array $parsed = null;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->query = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->files = $_FILES ?? [];
        $this->headers = $this->readHeaders();
        $this->body = (string)file_get_contents('php://input');
    }

    protected function readHeaders(): array
    {
        if (function_exists('getallheaders')) {
            $h = getallheaders();
            return is_array($h) ? $h : [];
        }
        $out = [];
        foreach ($_SERVER as $k => $v) {
            if (strpos($k, 'HTTP_') === 0) {
                $name = str_replace('_', '-', substr($k, 5));
                $out[$this->normalizeHeaderName($name)] = $v;
            }
        }
        return $out;
    }

    protected function normalizeHeaderName(string $name): string
    {
        $name = strtolower($name);
        $parts = explode('-', $name);
        foreach ($parts as $i => $p) {
            $parts[$i] = ucfirst($p);
        }
        return implode('-', $parts);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function json(): ?array
    {
        $data = json_decode($this->body, true);
        return is_array($data) ? $data : null;
    }

    protected function parseBody(): array
    {
        if ($this->parsed !== null) {
            return $this->parsed;
        }
        $ct = '';
        foreach ($this->headers as $k => $v) {
            if (strtolower($k) === 'content-type') {
                $ct = strtolower((string)$v);
                break;
            }
        }
        if ($ct !== '') {
            if (strpos($ct, 'application/json') !== false) {
                $j = json_decode($this->body, true);
                $this->parsed = is_array($j) ? $j : [];
                return $this->parsed;
            }
            if (strpos($ct, 'application/x-www-form-urlencoded') !== false) {
                $arr = [];
                parse_str($this->body, $arr);
                $this->parsed = is_array($arr) ? $arr : [];
                return $this->parsed;
            }
        }
        $this->parsed = [];
        return $this->parsed;
    }

    public function get($name = null, $default = null)
    {
        if ($name === null) {
            return $this->query;
        }
        if (array_key_exists($name, $this->query)) {
            return $this->query[$name];
        }
        if (is_string($name) && is_numeric($name)) {
            $n = (int)$name;
            if (array_key_exists($n, $this->query)) {
                return $this->query[$n];
            }
        }
        return $default;
    }

    public function post($name = null, $default = null)
    {
        if ($name === null) {
            return $this->post;
        }
        if (array_key_exists($name, $this->post)) {
            return $this->post[$name];
        }
        if (is_string($name) && is_numeric($name)) {
            $n = (int)$name;
            if (array_key_exists($n, $this->post)) {
                return $this->post[$n];
            }
        }
        return $default;
    }

    public function put($name = null, $default = null)
    {
        $data = $this->parseBody();
        if ($name === null) {
            return $data;
        }
        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    public function delete($name = null, $default = null)
    {
        $data = $this->parseBody();
        if ($name === null) {
            return $data;
        }
        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    public function patch($name = null, $default = null)
    {
        $data = $this->parseBody();
        if ($name === null) {
            return $data;
        }
        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    public function options($name = null, $default = null)
    {
        $data = $this->parseBody();
        if ($name === null) {
            return $data;
        }
        return array_key_exists($name, $data) ? $data[$name] : $default;
    }

    public function any($name = null, $default = null)
    {
        $data = $this->parseBody();
        $all = $this->method === 'GET' ? $this->query : ($this->method === 'POST' ? $this->post : $data);
        if ($name === null) {
            return $all;
        }
        if (array_key_exists($name, $all)) {
            return $all[$name];
        }
        if (is_string($name) && is_numeric($name)) {
            $n = (int)$name;
            if (array_key_exists($n, $all)) {
                return $all[$n];
            }
        }
        return $default;
    }

    public function header(string $name, $default = null)
    {
        $n = $this->normalizeHeaderName($name);
        return array_key_exists($n, $this->headers) ? $this->headers[$n] : $default;
    }

    public function headers(): array
    {
        return $this->headers;
    }

    public function cookie(string $name, $default = null)
    {
        return array_key_exists($name, $this->cookies) ? $this->cookies[$name] : $default;
    }

    public function cookies(): array
    {
        return $this->cookies;
    }

    public function files(): array
    {
        return $this->files;
    }

    public function file(string $name)
    {
        return array_key_exists($name, $this->files) ? $this->files[$name] : null;
    }
}
