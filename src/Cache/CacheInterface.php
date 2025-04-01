<?php

namespace YG\WSServer\Cache;

interface CacheInterface
{
    /**
     * 获取缓存
     * @param string $key
     * @return mixed
     */
    public function get(string $key);

    /**
     * 设置缓存
     * @param string $key
     * @param mixed $value
     * @param int $ttl 过期时间（秒）
     * @return bool
     */
    public function set(string $key, $value, int $ttl): bool;

    /**
     * 删除缓存
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool;
} 