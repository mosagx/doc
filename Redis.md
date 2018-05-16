# [DOC](http://doc.redisfans.com/)
#### 连接操作相关的命令
- 默认直接连接  远程连接-h 192.168.1.20 -p 6379
- **ping**：测试连接是否存活如果正常会返回pong
- **echo**：打印
- **select**：切换到指定的数据库，数据库索引号 index 用数字值指定，以 0 作为起始索引值
- **quit**：关闭连接（connection）
- **auth**：简单密码认证
---
#### 服务端相关命令
- **time**：返回当前服务器时间
- **client list**: 返回所有连接到服务器的客户端信息和统计数据  [参见](http://redisdoc.com/server/client_list.html)
- **client** kill ip:port：关闭地址为 ip:port 的客户端
- **save**：将数据同步保存到磁盘
- **bgsave**：将数据异步保存到磁盘
- **lastsave**：返回上次成功将数据保存到磁盘的Unix时戳
- **shundown**：将数据同步保存到磁盘，然后关闭服务
- **info**：提供服务器的信息和统计
- **config** resetstat：重置info命令中的某些统计数据
- **config get**：获取配置文件信
- **config set**：动态地调整 Redis 服务器的配置(configuration)而无须重启，可以修改的配置参数可以使用命令 CONFIG GET * 来列出
- **config rewrite**：Redis 服务器时所指定的 redis.conf 文件进行改写
- **monitor**：实时转储收到的请求  
- **slaveof**：改变复制策略设置
---
#### 发布订阅相关命令
- **psubscribe**：订阅一个或多个符合给定模式的频道 例如psubscribe news.* tweet.*
- **publish**：将信息 message 发送到指定的频道 channel 例如publish msg "good morning"
- **pubsub** channels：列出当前的活跃频道 例如PUBSUB CHANNELS news.i*
- **pubsub numsub**：返回给定频道的订阅者数量 例如PUBSUB NUMSUB news.it news.internet news.sport news.music
- **pubsub numpat**：返回客户端订阅的所有模式的数量总和
- **punsubscribe**：指示客户端退订所有给定模式。
- **subscribe**：订阅给定的一个或多个频道的信息。例如 subscribe msg chat_room
- **unsubscribe**：指示客户端退订给定的频道。
---

#### 对KEY操作的命令
- **exists**(key)：确认一个key是否存在
- **del**(key)：删除一个key
- **type**(key)：返回值的类型
- **keys**(pattern)：返回满足给定pattern的所有key
- **randomkey**：随机返回key空间的一个
- **keyrename**(oldname, newname)：重命名key
- **dbsize**：返回当前数据库中key的数目
- **expire**：设定一个key的活动时间（s）
- **ttl**：获得一个key的活动时间
- **move**(key, dbindex)：移动当前数据库中的key到dbindex数据库
- **flushdb**：删除当前选择数据库中的所有key
- **flushall**：删除所有数据库中的所有key

---
#### 获取慢查询

**SLOWLOG GET 10**

结果为查询ID、发生时间、运行时长和原命令 默认10毫秒，默认只保留最后的128条。单线程的模型下，一个请求占掉10毫秒是件大事情，注意设置和显示的单位为微秒，注意这个时间是不包含网络延迟的。
- slowlog get 获取慢查询日志
- slowlog len 获取慢查询日志条数
- slowlog reset 清空慢查询 

**配置**：

> config set slow-log-slower-than 20000

> config set slow-max-len 1000

> config rewrite
---
#### 模拟oom
> redis-cli debug oom
 
#### 模拟宕机
> redis-cli debug segfault
 
#### 模拟hang
> redis-cli -p 6379 DEBUG sleep 30
#### query在线分析 
> redis-cli MONITOR | head -n 5000 | ./redis-faina.py 

监控正在请求执行的命令,在cli下执行monitor，生产环境慎用。