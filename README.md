# YG WebSocket Server SDK

这是一个用于快速接入YG WebSocket服务的PHP SDK。

## 安装

使用Composer安装：

```bash
composer require yg/websocket-server-sdk
```

## 系统要求

- PHP >= 7.4
- Composer
- PHP JSON扩展
- PHP OpenSSL扩展

## 使用方法

### 1. 创建客户端实例

```php
use YG\WebSocketClient\YGWSClient;

$client = new YGWSClient(
    'your_app_id',
    'your_app_secret',
    'http://your-server'  // 可选，默认为 http://your-server
);
```

### 2. 注册用户

```php
try {
    $userData = $client->registerUser(
        'username',
        'password',
        'email@example.com',  // 可选
        [                     // 可选
            'phone' => '13800138000',
            'other_info' => '其他信息'
        ]
    );
    
    print_r($userData);
} catch (Exception $e) {
    echo "错误：" . $e->getMessage();
}
```

### 3. 获取WebSocket令牌

```php
try {
    $wsToken = $client->getWsToken($userData['user_id']);
    print_r($wsToken);
} catch (Exception $e) {
    echo "错误：" . $e->getMessage();
}
```

## 完整示例

查看 `examples/example.php` 文件获取完整的使用示例。

## 注意事项

1. 安全性
   - 请妥善保管 `app_secret`
   - 使用HTTPS进行API调用
   - 定期更新服务器令牌
   - 及时处理过期的WebSocket令牌
   - 密码使用SHA256加密存储

2. 错误处理
   - 处理网络异常情况
   - 记录关键错误日志
   - 处理用户状态检查（如用户被禁用）

3. 性能优化
   - 避免频繁获取WebSocket令牌
   - 及时关闭不需要的连接

## 错误码说明

| 错误码 | 说明 |
|--------|------|
| 400 | 请求参数错误（如用户名已存在、邮箱已被使用） |
| 401 | 未授权或令牌无效（如服务器令牌无效、用户被禁用） |
| 403 | 权限不足 |
| 404 | 资源不存在（如应用不存在或未激活） |
| 500 | 服务器内部错误 |

## 技术支持

如有问题，请联系技术支持：
- 邮箱：support@example.com
- 电话：400-xxx-xxxx 