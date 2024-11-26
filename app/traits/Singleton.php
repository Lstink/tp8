<?php

namespace app\traits;

trait Singleton
{
    // 保存类的唯一实例
    private static ?self $instance = null;

    // 禁止外部实例化
    private function __construct()
    {
    }

    // 禁止克隆
    private function __clone()
    {
    }

    // 禁止反序列化
    private function __wakeup()
    {
    }

    // 获取单例实例
    public static function getInstance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}
