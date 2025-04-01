<?php

namespace YG\WSServer\Cache;

use Predis\Client;

class RedisCache implements CacheInterface
{
    private Client $redis;

    public function __construct(array $config = [])
    {
        $this->redis = new Client($config);
    }

    public function get(string $key)
    {
        $value = $this->redis->get($key);
        return $value ? json_decode($value, true) : null;
    }

    public function set(string $key, $value, int $ttl): bool
    {
        return $this->redis->setex($key, $ttl, json_encode($value)) === 'OK';
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }
} 