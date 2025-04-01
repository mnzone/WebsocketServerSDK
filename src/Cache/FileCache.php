<?php

namespace YG\WSServer\Cache;

class FileCache implements CacheInterface
{
    private string $cacheDir;

    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir ?? sys_get_temp_dir() . '/yg_ws_cache';
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }

    public function get(string $key)
    {
        $file = $this->getCacheFile($key);
        if (!file_exists($file)) {
            return null;
        }

        $data = file_get_contents($file);
        if ($data === false) {
            return null;
        }

        $cache = json_decode($data, true);
        if (!$cache || !isset($cache['expires_at']) || !isset($cache['value'])) {
            return null;
        }

        if (time() > strtotime($cache['expires_at'])) {
            $this->delete($key);
            return null;
        }

        return $cache['value'];
    }

    public function set(string $key, $value, int $ttl): bool
    {
        $file = $this->getCacheFile($key);
        $data = [
            'value' => $value,
            'expires_at' => date('Y-m-d H:i:s', time() + $ttl)
        ];

        return file_put_contents($file, json_encode($data)) !== false;
    }

    public function delete(string $key): bool
    {
        $file = $this->getCacheFile($key);
        if (file_exists($file)) {
            return unlink($file);
        }
        return true;
    }

    private function getCacheFile(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
} 