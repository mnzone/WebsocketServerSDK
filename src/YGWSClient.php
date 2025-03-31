<?php

namespace YG\WebSocketClient;

use GuzzleHttp\Client;
use Ratchet\Client\WebSocket;
use React\EventLoop\Factory;
use React\Promise\PromiseInterface;

class YGWSClient
{
    private string $appId;
    private string $appSecret;
    private string $baseUrl;
    private ?string $serverToken = null;
    private ?string $serverTokenExpiresAt = null;
    private Client $httpClient;

    public function __construct(string $appId, string $appSecret, string $baseUrl = 'https://chat.yungangunite.com')
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
        ]);
    }

    /**
     * 获取服务器令牌
     * @return string
     * @throws \Exception
     */
    public function getServerToken(): string
    {
        // 如果令牌未过期，直接返回
        if ($this->serverToken && $this->serverTokenExpiresAt && time() < strtotime($this->serverTokenExpiresAt)) {
            return $this->serverToken;
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

            $this->serverToken = $data['access_token'];
            $this->serverTokenExpiresAt = date('Y-m-d H:i:s', time() + $data['expires_in']);

            return $this->serverToken;
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