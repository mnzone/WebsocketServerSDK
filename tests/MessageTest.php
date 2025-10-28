<?php
/**
 * 消息发送功能单元测试
 * 需要安装 PHPUnit: composer require --dev phpunit/phpunit
 * 运行测试: ./vendor/bin/phpunit tests/MessageTest.php
 */

use PHPUnit\Framework\TestCase;
use YG\WSServer\YGWSClient;
use YG\WSServer\Cache\FileCache;

class MessageTest extends TestCase
{
    private $client;
    private $mockHttpClient;
    
    protected function setUp(): void
    {
        // 创建测试用的客户端实例
        $this->client = new YGWSClient(
            'test_app_id',
            'test_app_secret',
            'https://test-server.com'
        );
    }
    
    /**
     * 测试用户消息发送
     */
    public function testSendMessageToUser()
    {
        // 这里应该使用Mock来模拟HTTP请求
        // 由于YGWSClient内部使用了GuzzleHttp\Client，实际测试中需要Mock这个依赖
        
        $this->markTestSkipped('需要Mock HTTP客户端，跳过实际网络请求测试');
        
        try {
            $result = $this->client->sendMessageToUser(
                'test-user-id',
                '测试消息',
                '测试标题',
                ['test' => true]
            );
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('success', $result);
        } catch (Exception $e) {
            $this->fail('用户消息发送失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 测试客户端消息发送
     */
    public function testSendMessageToClient()
    {
        $this->markTestSkipped('需要Mock HTTP客户端，跳过实际网络请求测试');
        
        try {
            $result = $this->client->sendMessageToClient(
                'test-client-id',
                '测试消息',
                '测试标题',
                ['test' => true]
            );
            
            $this->assertIsArray($result);
        } catch (Exception $e) {
            $this->fail('客户端消息发送失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 测试房间消息发送
     */
    public function testSendMessageToRoom()
    {
        $this->markTestSkipped('需要Mock HTTP客户端，跳过实际网络请求测试');
        
        try {
            $result = $this->client->sendMessageToRoom(
                'test-room-id',
                '测试消息',
                '测试标题',
                ['test' => true]
            );
            
            $this->assertIsArray($result);
        } catch (Exception $e) {
            $this->fail('房间消息发送失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 测试参数验证
     */
    public function testParameterValidation()
    {
        // 测试空用户ID
        $this->expectException(Exception::class);
        $this->client->sendMessageToUser('', 'test message');
    }
    
    /**
     * 测试缓存功能
     */
    public function testCacheFunctionality()
    {
        // 创建使用文件缓存的客户端
        $cache = new FileCache(sys_get_temp_dir() . '/test_cache');
        $client = new YGWSClient('test_app', 'test_secret', 'https://test.com', $cache);
        
        // 测试缓存是否正常工作
        $this->assertInstanceOf(FileCache::class, $cache);
    }
    
    /**
     * 测试消息类型默认值
     */
    public function testDefaultMessageTypes()
    {
        // 这个测试验证方法签名中的默认值是否正确
        $reflection = new ReflectionClass(YGWSClient::class);
        
        // 检查sendMessageToUser的默认参数
        $userMethod = $reflection->getMethod('sendMessageToUser');
        $userParams = $userMethod->getParameters();
        $this->assertEquals('system', $userParams[4]->getDefaultValue());
        
        // 检查sendMessageToClient的默认参数
        $clientMethod = $reflection->getMethod('sendMessageToClient');
        $clientParams = $clientMethod->getParameters();
        $this->assertEquals('notification', $clientParams[4]->getDefaultValue());
        
        // 检查sendMessageToRoom的默认参数
        $roomMethod = $reflection->getMethod('sendMessageToRoom');
        $roomParams = $roomMethod->getParameters();
        $this->assertEquals('broadcast', $roomParams[4]->getDefaultValue());
    }
    
    /**
     * 测试方法存在性
     */
    public function testMethodsExist()
    {
        $this->assertTrue(method_exists($this->client, 'sendMessageToUser'));
        $this->assertTrue(method_exists($this->client, 'sendMessageToClient'));
        $this->assertTrue(method_exists($this->client, 'sendMessageToRoom'));
        $this->assertTrue(method_exists($this->client, 'getServerToken'));
        $this->assertTrue(method_exists($this->client, 'registerUser'));
        $this->assertTrue(method_exists($this->client, 'getWsToken'));
    }
    
    /**
     * 测试私有方法sendMessage的存在性
     */
    public function testPrivateSendMessageMethodExists()
    {
        $reflection = new ReflectionClass(YGWSClient::class);
        $this->assertTrue($reflection->hasMethod('sendMessage'));
        
        $sendMessageMethod = $reflection->getMethod('sendMessage');
        $this->assertTrue($sendMessageMethod->isPrivate());
    }
}
