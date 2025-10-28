<?php
/**
 * 快速测试脚本 - 消息发送功能
 * 使用方法: php quick_test.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use YG\WSServer\YGWSClient;

// 配置参数 - 请修改为您的实际参数
$config = [
    'app_id' => 'avanti-takeout',
    'app_secret' => 'ac551bf13499be9a5022db0f4b07f169',
    'base_url' => 'https://avt-chat.yungangunite.com',
    'test_user_id' => 'b14ef993-bb0f-4a78-aed7-40b0a7403322',
    'test_client_id' => 'da3e4e13-ab3e-47d1-b876-d12d8776e29a',
    'test_room_id' => 'announcements'
];

echo "🚀 开始快速测试消息发送功能\n";
echo "================================\n\n";

// 创建客户端实例
try {
    $client = new YGWSClient(
        $config['app_id'],
        $config['app_secret'],
        $config['base_url']
    );
    echo "✅ 客户端初始化成功\n";
} catch (Exception $e) {
    echo "❌ 客户端初始化失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 测试1: 获取服务器令牌
echo "\n📋 测试1: 获取服务器令牌\n";
echo "------------------------\n";
try {
    $token = $client->getServerToken();
    echo "✅ 服务器令牌获取成功\n";
    echo "令牌前缀: " . substr($token, 0, 20) . "...\n";
} catch (Exception $e) {
    echo "❌ 服务器令牌获取失败: " . $e->getMessage() . "\n";
    exit(1);
}

// 测试2: 发送用户消息
echo "\n📤 测试2: 发送用户消息\n";
echo "----------------------\n";
try {
    $result = $client->sendMessageToUser(
        $config['test_user_id'],
        '这是一条测试用户消息',
        '用户测试通知',
        ['test_type' => 'user_message', 'timestamp' => date('Y-m-d H:i:s')]
    );
    echo "✅ 用户消息发送成功\n";
    echo "响应: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "❌ 用户消息发送失败: " . $e->getMessage() . "\n";
}

// 测试3: 发送客户端消息
echo "\n📱 测试3: 发送客户端消息\n";
echo "------------------------\n";
try {
    $result = $client->sendMessageToClient(
        $config['test_client_id'],
        '这是一条测试客户端消息',
        '客户端测试通知',
        ['test_type' => 'client_message', 'timestamp' => date('Y-m-d H:i:s')]
    );
    echo "✅ 客户端消息发送成功\n";
    echo "响应: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "❌ 客户端消息发送失败: " . $e->getMessage() . "\n";
}

// 测试4: 发送房间消息
echo "\n🏠 测试4: 发送房间消息\n";
echo "----------------------\n";
try {
    $result = $client->sendMessageToRoom(
        $config['test_room_id'],
        '这是一条测试房间消息',
        '房间测试公告',
        ['test_type' => 'room_message', 'timestamp' => date('Y-m-d H:i:s')]
    );
    echo "✅ 房间消息发送成功\n";
    echo "响应: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "❌ 房间消息发送失败: " . $e->getMessage() . "\n";
}

// 测试5: 测试缓存功能
echo "\n💾 测试5: 测试缓存功能\n";
echo "----------------------\n";
try {
    $start = microtime(true);
    $client->getServerToken();
    $firstCall = microtime(true) - $start;
    
    $start = microtime(true);
    $client->getServerToken();
    $secondCall = microtime(true) - $start;
    
    echo "第一次调用耗时: " . round($firstCall * 1000, 2) . "ms\n";
    echo "第二次调用耗时: " . round($secondCall * 1000, 2) . "ms\n";
    
    if ($secondCall < $firstCall) {
        echo "✅ 缓存工作正常，加速比: " . round($firstCall / $secondCall, 2) . "x\n";
    } else {
        echo "⚠️  缓存可能未生效\n";
    }
} catch (Exception $e) {
    echo "❌ 缓存测试失败: " . $e->getMessage() . "\n";
}

// 测试6: 错误处理测试
echo "\n⚠️  测试6: 错误处理测试\n";
echo "----------------------\n";
try {
    $client->sendMessageToUser('', '测试消息');
    echo "❌ 应该抛出异常但没有\n";
} catch (Exception $e) {
    echo "✅ 错误处理正常: " . $e->getMessage() . "\n";
}

echo "\n🎉 测试完成！\n";
echo "============\n";
echo "如果所有测试都显示 ✅，说明消息发送功能工作正常。\n";
echo "如果有 ❌ 或 ⚠️，请检查配置参数和网络连接。\n";
echo "\n💡 提示:\n";
echo "- 请确保修改了配置参数中的 app_id 和 app_secret\n";
echo "- 确保测试用的用户ID、客户端ID和房间ID是有效的\n";
echo "- 检查网络连接和服务器地址是否正确\n";
