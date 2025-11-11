<?php
namespace PhotonixCore;

class Data
{
    /**
     * 检测是否为一个邮箱
     * @param string $email
     * @return bool
     */
    public static function isEmail(string $email): bool
    {
        // 去除首尾空白字符
        $email = trim($email);

        // 使用PHP内置过滤器验证邮箱格式
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}