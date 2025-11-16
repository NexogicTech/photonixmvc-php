<?php

namespace App;

class baseController
{
    /**
     * 通过数组转为json字符串返回（数据与code平级）
     * @param array $data
     * @param int $code
     * @param bool $isShowCode 是否显示code字段
     * @return string
     */
    public function json(array $data, int $code = 200, bool $isShowCode = true): string
    {
        // 根据 $isShowCode 决定是否添加 code 字段
        $response = $isShowCode ? ['code' => $code] : [];
        // 合并数据（若数据中存在 code 键，且 $isShowCode 为 true 时会被覆盖）
        $response = array_merge($response, $data);
        // 转换为JSON并保留中文
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }
}