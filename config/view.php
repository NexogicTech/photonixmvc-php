<?php
return [
    // 视图渲染器
    "view_engine" => "PhotonixView-v2", // 可选列表 ["PhotonixView-v2","PhotonixView-lite"] // 1功能多 2性能高
    // 视图后缀名
    "view_suffix" => ".tpl",
    // 渲染左标记(有空格)
    "view_display_l" => "{{ ",
    // 渲染右标记(有空格)
    "view_display_r" => " }}",
    // 渲染代码左标记(有空格)
    "view_code_display_l" => "{% ",
    // 渲染代码右标记(有空格)
    "view_code_display_r" => " %} ",

    // 启模板动编译
    "view_code_use_cached" => true
];