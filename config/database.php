<?php
use PhotonixCore\Env;

return [
    // 数据库种类
    "sql_type" => Env::env("DB_TYPE", "mysql"),
    // 数据库地址
    "sql_host" => Env::env("DB_HOST", "localhost"),
    // 数据库用户名
    "sql_user" => Env::env("DB_USER", "root"),
    // 数据库密码
    "sql_password" => Env::env("DB_PASSWORD", ""),
    // 数据库编码
    "sql_database" => Env::env("DB_DATABASE", "utf-8"),
    // 数据库端口
    "sql_port" => Env::env("DB_PORT", "3306"),
 ];