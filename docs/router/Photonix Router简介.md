# Photonix Router 简介

Photonix Router 是框架的核心调度组件，负责将浏览器请求映射到业务控制器方法并输出响应。它强调简洁的注册方式、直观的匹配规则与友好的参数传递能力，适合中小型 Web 与 API 项目。

- 路由集中注册在 `app/routes/app.php`，调度前自动一次性加载。
- 支持多方法：`get/post/put/delete/patch/options/any`。
- 匹配顺序：先精确匹配，再参数化匹配，最后输出 404。
- 参数化路由占位符 `{name}`，支持多段与具名提取，传入控制器形参。
- 控制器标识采用 `Namespace\\Class/method`，可选显式 `path` 文件路径（相对 `app/`）。
- 文件路径参数可省略，框架会自动解析控制器类对应的文件并加载。
- 支持中文路径参数，自动进行 URL 解码。

示例：

```
use PhotonixCore\\Routes;
Routes::get('/', 'App\\Home\\Controller\\Index/index');
Routes::get('/user/{id}', 'App\\User\\Controller\\Profile/show');
Routes::any('/ping', 'App\\Health\\Controller\\Probe/ping');
```
