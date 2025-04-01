<?php

require_once __DIR__ . '/../vendor/autoload.php';

use YG\WSServer\YGWSClient;
use YG\WSServer\Cache\FileCache;
use YG\WSServer\Cache\RedisCache;

// 方式1：使用默认的文件缓存
$client1 = new YGWSClient(
    'your_app_id',
    'your_app_secret',
    'http://your-server'
);

// 方式2：使用自定义目录的文件缓存
$client2 = new YGWSClient(
    'your_app_id',
    'your_app_secret',
    'http://your-server',
    new FileCache(__DIR__ . '/cache')
);

// 方式3：使用Redis缓存（如果已安装predis/predis）
try {
    $redisCache = new RedisCache([
        'scheme' => 'tcp',
        'host'   => '127.0.0.1',
        'port'   => 6379,
    ]);
    $client3 = new YGWSClient(
        'your_app_id',
        'your_app_secret',
        'http://your-server',
        $redisCache
    );
} catch (\Exception $e) {
    echo "Redis连接失败，将使用默认的文件缓存\n";
    $client3 = new YGWSClient(
        'your_app_id',
        'your_app_secret',
        'http://your-server'
    );
}

try {
    // 使用任意一个客户端实例进行操作
    $client = $client1; // 或 $client2 或 $client3

    // 注册用户
    $userData = $client->registerUser(
        'test_user',
        'test_password',
        'test@example.com',
        [
            'phone' => '13800138000',
            'other_info' => '其他信息'
        ]
    );

    echo "用户注册成功：\n";
    print_r($userData);

    // 获取WebSocket令牌
    $wsToken = $client->getWsToken($userData['user_id']);
    echo "WebSocket令牌：\n";
    print_r($wsToken);

} catch (Exception $e) {
    echo "错误：" . $e->getMessage() . "\n";
} 