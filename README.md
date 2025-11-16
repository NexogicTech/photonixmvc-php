# Photonix MVC 框架总述（v1.0.0）

> Photonix 是由 Nexogic 以“大简而美”为原则打造的轻量级 MVC PHP 框架，兼容 PHP 8.x，面向小到中型 Web 应用与 API 服务，强调易用与可扩展。

## 1. 框架概述

- 核心功能：
    - 路由调度（GET/POST/PUT/DELETE/PATCH/OPTIONS/ANY），支持参数化路由 `/user/{id}`。
    - 控制器调用（`Namespace\Class/method`），可选显式文件路径引入。
    - 错误页统一输出（404/500 等，来自 `config/error.php`）。
    - 环境变量加载（`PhotonixCore\Env`），统一配置管理。
    - 视图引擎（`PhotonixView-v2`，预留接口）。
    - Composer PSR-4 自动加载与 Apache/Nginx 重写支持。

- 技术栈：PHP 8.x、Composer（PSR-4）、Apache/Nginx、JSON/ENV 配置、轻量工具类。

- 适用场景：中小型网站、管理后台、原型验证、轻量 API 服务、教学与学习。
