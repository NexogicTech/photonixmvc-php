<?php
namespace Routes;

use PhotonixCore\Routes;

// 示例：在此处注册应用路由
// 根路径 -> App\Home\Controller\Index::index
Routes::get("/", "App\\Home\\Controller\\Index/index");
Routes::get("/{name}/hello/", "App\\Home\\Controller\\Index/hello");
// 允许不传 name，使用控制器方法默认值
Routes::get("/hello", "App\\Home\\Controller\\Index/hello");