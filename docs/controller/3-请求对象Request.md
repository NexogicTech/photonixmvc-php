# 请求对象 Request

- 注入：在方法参数中添加 `$request` 或类型 `PhotonixCore\\Request`
- 常用方法：
  - `method()` 当前方法
  - `get(name=null, default=null)` 获取 GET；不传 `name` 返回全部
  - `post(name=null, default=null)` 获取 POST；不传返回全部
  - `put/delete/patch/options(name=null, default=null)` 解析请求体（JSON 或表单）
  - `any(name=null, default=null)` 按当前方法取参数
  - `body()` 原始体、`json()` JSON 数组
  - `header(name, default)`、`headers()`
  - `cookie(name, default)`、`cookies()`
  - `file(name)`、`files()`