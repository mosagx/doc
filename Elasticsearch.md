### Doc
 - [Elasticsearch-PHP (En)](https://www.elastic.co/guide/en/elasticsearch/client/php-api/current/index.html)
 - [Elasticsearch Reference](https://nocf-www.elastic.co/guide/en/elasticsearch/reference/current/index.html)
- [Elasticsearch: 权威指南 (Cn)](https://www.elastic.co/guide/cn/elasticsearch/guide/current/index.html)
 - [中文社区](https://elasticsearch.cn/)

### Plugin
 - [Kibana](https://nocf-www.elastic.co/cn/products/kibana)
 - [X-Pack](https://nocf-www.elastic.co/cn/products/x-pack)
---
### 主机配置

##### 1、单节点配置
```
$hosts = [
    // https://username:password@domain.com:9200/
    [
        'host' => 'domain.com',
        'port' => '9200',
        'scheme' => 'https',
        'user' => 'username',
        'pass' => 'password'
    ],

    // This is equal to "http://localhost:9200/"
    [
        'host' => 'localhost',    // Only host is required
    ]
];
```
##### 2、多节点配置
```
$hosts = [
    '192.168.1.1:9200',         // IP + Port
    '192.168.1.2',              // Just IP
    'mydomain.server.com:9201', // Domain + Port
    'mydomain2.server.com',     // Just Domain
    'https://localhost',        // SSL to localhost
    'https://192.168.1.3:9200'  // SSL to IP + Port
];
```
##### 3、创建客户端对象
```
$clientBuilder = ClientBuilder::create();   // Instantiate a new ClientBuilder
$clientBuilder->setHosts($hosts);           // Set the hosts
$client = $clientBuilder->build();          // Build the client object
```

##### ---monolog
composer.json添加monolog组件
```
{
    "require": {
        ...
        "elasticsearch/elasticsearch" : "~5.0",
        "monolog/monolog": "~1.0"
    }
}
```
更新composer
```
php composer.phar update
```
php code
```
$logger = ClientBuilder::defaultLogger('path/to/your.log');
// set severity with second parameter
// $logger = ClientBuilder::defaultLogger('/path/to/logs/', Logger::INFO);

$client = ClientBuilder::create()       // Instantiate a new ClientBuilder
            ->setLogger($logger)        // Set the logger with a default logger
            ->build();                  // Build the client object
```
defaultLogger()
```
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('name');
$logger->pushHandler(new StreamHandler('path/to/your.log', Logger::WARNING));

$client = ClientBuilder::create()       // Instantiate a new ClientBuilder
            ->setLogger($logger)        // Set your custom logger
            ->build();                  // Build the client object
```
---
### Search
```
GET {index}/{type}/_search
{
  "size": 0,
  "query": {
    "range": {
      "created_at": {
        "gte": "2018-02-01 00:00:00",
        "lte": "2018-02-02 00:00:00"
      }
    }
  },
  "aggs": {
    "data": {
      "date_histogram": {
        "field": "created_at",
        "interval": "hour",
        "format" : "yyyy-MM-dd HH",
        "min_doc_count": 0
      },
      "aggs": {
        "uvs": {
          "cardinality": {
            "field": "ip",
            "precision_threshold" : 100
          }
        }
      }
    }
  }
}
```
