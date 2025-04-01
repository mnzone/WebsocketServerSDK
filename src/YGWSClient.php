<?php

namespace YG\WSServer;

use GuzzleHttp\Client;
use YG\WSServer\Cache\CacheInterface;
use YG\WSServer\Cache\FileCache;

class YGWSClient
{
    private string $appId;
    private string $appSecret;
    private string $baseUrl;
    private Client $httpClient;
    private CacheInterface $cache;
    private const CACHE_KEY_PREFIX = 'yg_ws_server_token_';

    public function __construct(
        string $appId, 
        string $appSecret, 
        string $baseUrl = 'https://chat.yungangunite.com',
        ?CacheInterface $cache = null
    ) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
        ]);
        $this->cache = $cache ?? new FileCache();
    }

    /**
     * 获取服务器令牌
     * @return string
     * @throws \Exception
     */
    public function getServerToken(): string
    {
        $cacheKey = self::CACHE_KEY_PREFIX . $this->appId;

        // 尝试从缓存获取令牌
        if ($this->cache) {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData && isset($cachedData['token']) && isset($cachedData['expires_at'])) {
                if (time() < strtotime($cachedData['expires_at'])) {
                    return $cachedData['token'];
                }
            }
        }

        try {
            $response = $this->httpClient->post('/cgi-bin/token', [
                'json' => [
                    'app_id' => $this->appId,
                    'app_secret' => $this->appSecret,
                    'grant_type' => 'client_credentials'
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (!isset($data['access_token'])) {
                throw new \Exception('Failed to get server token');
            }

            $token = $data['access_token'];
            $expiresAt = date('Y-m-d H:i:s', time() + $data['expires_in']);

            // 保存到缓存
            if ($this->cache) {
                $this->cache->set($cacheKey, [
                    'token' => $token,
                    'expires_at' => $expiresAt
                ], $data['expires_in']);
            }

            return $token;
        } catch (\Exception $e) {
            throw new \Exception('Failed to get server token: ' . $e->getMessage());
        }
    }

    /**
     * 注册用户
     * @param string $username
     * @param string $password
     * @param string|null $email
     * @param array|null $metadata
     * @return array
     * @throws \Exception
     */
    public function registerUser(string $username, string $password, ?string $email = null, ?array $metadata = null): array
    {
        try {
            $response = $this->httpClient->post('/cgi-bin/add-user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getServerToken()
                ],
                'json' => [
                    'username' => $username,
                    'password' => hash('sha256', $password),
                    'email' => $email,
                    'metadata' => $metadata
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception('Failed to register user: ' . $e->getMessage());
        }
    }

    /**
     * 获取WebSocket连接令牌
     * @param string $userId
     * @return array
     * @throws \Exception
     */
    public function getWsToken(string $userId): array
    {
        try {
            $response = $this->httpClient->post('/cgi-bin/ws-token', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getServerToken()
                ],
                'json' => [
                    'user_id' => $userId
                ]
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            throw new \Exception('Failed to get WebSocket token: ' . $e->getMessage());
        }
    }
} 