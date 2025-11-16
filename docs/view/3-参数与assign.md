# 参数传递与 assign

## 控制器传参

```php
use PhotonixCore\View\View;
return View::display('home/index', 'cached', [
    'title' => '你好',
    'user' => ['name' => '张三'],
    'list' => [1,2,3],
]);
```

## 全局赋值（控制器）

```php
use PhotonixCore\View\View;
View::assign('site.name', 'Photonix');
View::assign('user.name', '李四');
return View::display('home/index');
```

## 模板内赋值

```tpl
{assign var="title" value="欢迎"}
{assign var="user.name" value=$title}
```