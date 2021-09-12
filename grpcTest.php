<?php
require dirname(__FILE__) . '/vendor/autoload.php';
/*
$client = new \Go\Micro\Srv\Morder\MorderClient("192.168.78.134:21002",['credentials' => Grpc\ChannelCredentials::createInsecure()]);
$request = new \Go\Micro\Srv\Morder\CreateOrderRequest();
$request->setNum(1);
$request->setServiceBid(100000200275);
$request->setPayBid(100000200117);
$request->setUnitPrice(100);
list($response, $status) = $client->CreateOrder($request)->wait();
var_dump($response, $status);
// echo sprintf("code: %s, msg: %s \n", $response->getCode(), $response->getMsg());
*/

$client = new \Go\Micro\Srv\Test\TestClient("192.168.78.134:6666",['credentials' => \Grpc\ChannelCredentials::createInsecure()]);
$rpcRequest = new \Go\Micro\Srv\Test\UserRequest();
var_dump($client);
// 获取参数
$rpcRequest->setId(1);
var_dump("请求之前");
list($rpcResponse, $status) = $client->GetUserInfo($rpcRequest)->wait();
var_dump($rpcResponse, $status);
?>