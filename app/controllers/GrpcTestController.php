<?php
namespace App\controllers;
use Core\annotations\Bean;
use Core\annotations\RequestMapping;
use Core\http\Request;
use Core\http\Response;

/**
 * @Bean()
 */
class GrpcTestController {
    /**
     * @RequestMapping(value="/test/grpcTest",method={})
     */
    public function grpcTest(Request $request, Response $response) {
        $client = new \Go\Micro\Srv\Test\TestClient("192.168.78.134:6666",['credentials' => \Grpc\ChannelCredentials::createInsecure()]);
        $rpcRequest = new \Go\Micro\Srv\Test\UserRequest();
        // 获取参数
        $params = $request->getQueryParams();
        $rpcRequest->setId($params["id"]);
        list($rpcResponse, $status) = $client->GetUserInfo($rpcRequest)->wait();
        var_dump($rpcResponse, $status);
        return $rpcResponse;
    }
}