<?php

require_once __DIR__ . '/vendor/autoload.php';

use YG\WSServer\YGWSClient;

// 创建客户端实例
$client = new YGWSClient(
    'your_app_id',
    'your_app_secret',
    'https://avt-chat.yungangunite.com'
);

try {
    // 发送消息给指定用户
    $userResult = $client->sendMessageToUser(
        'b14ef993-bb0f-4a78-aed7-40b0a7403322', // 目标用户ID
        '这是一条系统消息', // 消息内容
        '系统通知', // 消息标题
        [ // 附加数据
            'action' => 'notification',
            'priority' => 'high'
        ]
    );
    
    echo "用户消息发送成功：\n";
    print_r($userResult);
    
    // 发送消息给指定客户端
    $clientResult = $client->sendMessageToClient(
        '837b39e1-54fc-4667-bc12-4e263a5cf7b4', // 目标客户端ID
        '这是一条通知消息', // 消息内容
        '重要通知', // 消息标题
        [ // 附加数据
            'action' => 'update_required',
            'url' => 'https://example.com/update'
        ]
    );
    
    echo "客户端消息发送成功：\n";
    print_r($clientResult);
    
    // 发送消息给指定房间的所有用户
    $roomResult = $client->sendMessageToRoom(
        'room_123', // 目标房间ID
        '房间公告：系统将在10分钟后维护', // 消息内容
        '系统维护通知', // 消息标题
        [ // 附加数据
            'maintenance_time' => '2025-10-27 21:00:00',
            'duration' => '30分钟'
        ]
    );
    
    echo "房间消息发送成功：\n";
    print_r($roomResult);
    
} catch (Exception $e) {
    echo "错误：" . $e->getMessage() . "\n";
}
