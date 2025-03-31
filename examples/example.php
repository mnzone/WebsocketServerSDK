<?php

require_once __DIR__ . '/../vendor/autoload.php';

use YG\WSServer\YGWSClient;

// 创建WebSocket客户端实例
$client = new YGWSClient(
    'your_app_id',
    'your_app_secret',
    'http://your-server'
);

try {
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