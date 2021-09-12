# simpleHttpSwoole
一个简单的，基于swoole的http服务器

## 功能
- 基于swoole （√）
- 支持路由注解 （√）
- 自动热性能 （√）
- 支持参数校验
- 支持grcp（√）

## 开始使用
```
php boot.php start
```

## 路由
```
/**
 * @RequestMapping(value="/test",method={})
 */
public function test() {
    // local:8080/test
    return "test";
}
```

## grpc使用
### 生成proto文件

```
protoc --php_out=/项目路径/ --grpc_out=/项目路径/ --plugin=protoc-gen-grpc=/项目路径/app/bin/grpc_php_plugin test.proto
```

### 开启服务端(go写的测试文件，执行运行即可)
```
/项目路径app/bin/main
```

### 调用
```
/**
 * @RequestMapping(value="/test/grpcTest",method={})
 */
public function grpcTest(Request $request, Response $response) {
   $client = new \Go\Micro\Srv\Test\TestClient("xxxx:xxx",['credentials' => \Grpc\ChannelCredentials::createInsecure()]);
   $rpcRequest = new \Go\Micro\Srv\Test\UserRequest();
   // 获取参数
   $params = $request->getQueryParams();
   $rpcRequest->setId($params["id"]);
   list($rpcResponse, $status) = $client->GetUserInfo($rpcRequest)->wait();
   return $rpcResponse;
}
```