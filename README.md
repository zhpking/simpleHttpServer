# simpleHttpSwoole
一个简单的，基于swoole的http服务器

## 功能
- 基于swoole
- 支持路由注解
- 自动热更新

## 开始使用
```
php boot.php start
```

## 路由
```php
/**
 * @RequestMapping(value="/test",method={})
 */
public function test() {
    // local:8080/test
    return "test";
}
```