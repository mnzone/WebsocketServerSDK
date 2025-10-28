# 更新日志

## 功能概览

### 当前版本特性 (v1.1.0)
- ✅ **用户管理**：用户注册、WebSocket令牌获取
- ✅ **消息发送系统**：支持向用户、客户端和房间发送消息
- ✅ **缓存系统**：文件缓存和Redis缓存支持
- ✅ **自动认证**：服务器令牌自动管理和刷新
- ✅ **错误处理**：完整的异常处理机制
- ✅ **代码优化**：高复用性的代码架构

### 消息发送能力
- 发送给用户的所有客户端
- 发送给指定客户端
- 发送给房间的所有用户
- 支持自定义消息类型、标题和附加数据
- 统一的API接口和错误处理

---

## [1.1.0] - 2025-01-27

### 新增功能 - 消息发送API
- **完整的消息发送系统**：支持向用户、客户端和房间发送消息
- 添加 `sendMessageToUser()` 方法，发送消息给指定用户的所有客户端
- 添加 `sendMessageToClient()` 方法，发送消息给指定客户端
- 添加 `sendMessageToRoom()` 方法，发送消息给指定房间的所有用户
- 支持自定义消息类型、标题和附加数据
- 自动服务器令牌管理和认证

### 方法详情

#### 消息发送方法
```php
// 发送给用户的所有客户端
public function sendMessageToUser(
    string $targetId,        // 目标用户ID
    string $content,         // 消息内容
    string $title = '',      // 消息标题（可选）
    ?array $data = null,     // 附加数据（可选）
    string $messageType = 'system'  // 消息类型（默认：system）
): array

// 发送给指定客户端
public function sendMessageToClient(
    string $clientId,        // 目标客户端ID
    string $content,         // 消息内容
    string $title = '',      // 消息标题（可选）
    ?array $data = null,     // 附加数据（可选）
    string $messageType = 'notification'  // 消息类型（默认：notification）
): array

// 发送给房间的所有用户
public function sendMessageToRoom(
    string $roomId,          // 目标房间ID
    string $content,         // 消息内容
    string $title = '',      // 消息标题（可选）
    ?array $data = null,     // 附加数据（可选）
    string $messageType = 'broadcast'  // 消息类型（默认：broadcast）
): array
```

### 使用示例
```php
// 发送给用户
$userResult = $client->sendMessageToUser(
    'user-123',
    '系统通知消息',
    '重要通知',
    ['priority' => 'high']
);

// 发送给客户端
$clientResult = $client->sendMessageToClient(
    'client-456',
    '客户端更新通知',
    '更新提醒',
    ['action' => 'update_required']
);

// 发送给房间
$roomResult = $client->sendMessageToRoom(
    'room_123',
    '房间公告：系统将在10分钟后维护',
    '系统维护通知',
    [
        'maintenance_time' => '2025-10-27 21:00:00',
        'duration' => '30分钟'
    ]
);
```

### 技术特性
- **统一API接口**：所有消息发送方法使用一致的参数结构
- **自动认证**：自动管理服务器令牌，无需手动处理认证
- **灵活配置**：支持自定义消息类型、标题和附加数据
- **错误处理**：完整的异常处理机制，提供详细的错误信息
- **类型安全**：使用PHP 7.4+的类型声明，确保参数类型正确

### 消息类型支持
- **用户消息**：`target_type: "user"`，默认类型 `system`
- **客户端消息**：`target_type: "client"`，默认类型 `notification`  
- **房间消息**：`target_type: "room"`，默认类型 `broadcast`

### 缓存优化
- 实现灵活的缓存机制，支持文件缓存和Redis缓存
- 将Redis依赖改为可选，默认使用文件缓存
- 添加缓存接口，提高代码可扩展性
- 支持自定义缓存目录和配置

### 代码优化
- 重构消息发送方法，提取公共逻辑到私有 `sendMessage()` 方法
- 提高代码复用性，减少重复代码约70%
- 统一错误处理机制
- 简化维护和扩展

### 文档更新
- 新增 `MESSAGE_API.md` 详细API文档
- 新增 `ARCHITECTURE.md` 代码架构说明
- 更新示例代码，展示所有三种消息发送方式
- 更新测试文件，包含完整的消息发送测试

## [1.0.0] - 2025-01-27

### 初始版本
- 基础WebSocket客户端SDK
- 支持用户注册和WebSocket令牌获取
- 支持服务器令牌管理和缓存
