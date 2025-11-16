# Photonix MVC 框架总述（v0.9.2）

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

## 2. 命名规范

- 目录和文件
  - 目录使用驼峰命名；
  - 类库、函数文件统一以.php为后缀；
  - 类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致（改为首字母daxie) ；
  - 类（包含接口和Trait）文件采用驼峰法命名（首字母大写），其它文件采用小写+下划线命名；
  - 类名（包括接口和Trait）和文件名保持一致，统一采用驼峰法命名（首字母大写）；
- 函数和类、属性命名
  - 类的命名采用驼峰法（首字母大写），例如 User、UserType；
  - 函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 get_client_ip；
  - 方法的命名使用驼峰法（首字母小写），例如 getUserName；
  - 属性的命名使用驼峰法（首字母小写），例如 tableName、instance；
  - 特例：以双下划线__打头的函数或方法作为魔术方法，例如 __call 和 __autoload；
- 常量和配置
  - 常量以大写字母和下划线命名，例如 APP_PATH；
  - 配置参数以小写字母和下划线命名，例如 url_route_on 和url_convert；
  - 环境变量定义使用大写字母和下划线命名，例如APP_DEBUG；

(c) 2025 nexogic.org & photonix-mvc.cn
