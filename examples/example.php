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

    // 发送消息给指定用户
    $messageResult = $client->sendMessageToUser(
        $userData['user_id'], // 目标用户ID
        '这是一条系统消息', // 消息内容
        '系统通知', // 消息标题
        [ // 附加数据
            'action' => 'notification',
            'priority' => 'high'
        ]
    );
    echo "消息发送结果：\n";
    print_r($messageResult);

    // 发送简单消息（只传必需参数）
    $simpleMessage = $client->sendMessageToUser(
        $userData['user_id'],
        '这是一条简单消息'
    );
    echo "简单消息发送结果：\n";
    print_r($simpleMessage);

    // 发送消息给指定客户端
    $clientMessage = $client->sendMessageToClient(
        '837b39e1-54fc-4667-bc12-4e263a5cf7b4', // 目标客户端ID
        '这是一条通知消息', // 消息内容
        '重要通知', // 消息标题
        [ // 附加数据
            'action' => 'update_required',
            'url' => 'https://example.com/update'
        ]
    );
    echo "客户端消息发送结果：\n";
    print_r($clientMessage);

    // 发送简单客户端消息（只传必需参数）
    $simpleClientMessage = $client->sendMessageToClient(
        '837b39e1-54fc-4667-bc12-4e263a5cf7b4',
        '这是一条简单客户端消息'
    );
    echo "简单客户端消息发送结果：\n";
    print_r($simpleClientMessage);

    // 发送消息给指定房间的所有用户
    $roomMessage = $client->sendMessageToRoom(
        'room_123', // 目标房间ID
        '房间公告：系统将在10分钟后维护', // 消息内容
        '系统维护通知', // 消息标题
        [ // 附加数据
            'maintenance_time' => '2025-10-27 21:00:00',
            'duration' => '30分钟'
        ]
    );
    echo "房间消息发送结果：\n";
    print_r($roomMessage);

    // 发送简单房间消息（只传必需参数）
    $simpleRoomMessage = $client->sendMessageToRoom(
        'room_123',
        '这是一条简单房间消息'
    );
    echo "简单房间消息发送结果：\n";
    print_r($simpleRoomMessage);

} catch (Exception $e) {
    echo "错误：" . $e->getMessage() . "\n";
} 