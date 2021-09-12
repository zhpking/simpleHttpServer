<?php
require_once __DIR__ . "/vendor/autoload.php";
use Swoole\Http\Request;
use Swoole\Http\Response;

// 自定义配置
// require_once __DIR__ . "/app/config/define.php";
// 初始化Bean工厂
// \Core\BeanFactory::init();
// $dispatcher = \Core\BeanFactory::getBean("RouterCollector")->getDispatcher();

/*
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute("GET","/test",function(){
        // return "my test";
        return json_encode(array("bid" => 10000200275));
    });

    $r->addRoute("GET","/",function(){
        return "index test";
    });
});
*/

$http = new Swoole\Http\Server("0.0.0.0", 8080);
$http->on("request", function(Request $request, Response $response) use($dispatcher){
    /*
    $myrequest = \Core\http\Request::init($request);
    $myresponse = \Core\http\Response::init($response);
    // $routeInfo = $dispatcher->dispatch($request->server["request_method"], $request->server["request_uri"]);
    $routeInfo = $dispatcher->dispatch($myrequest->getMethod(), $myrequest->getUri());
    // $routeInfo[0]:请求返回码 $routeInfo[1]:请求方式 $routeInfo[2]:请求方法
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            $response->status(404);
            $response->end();
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            $allowedMethods = $routeInfo[1];
            $response->status(405);
            $response->end();
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            // var_dump($routeInfo);
            $vars = $routeInfo[2];
            // 设置额外参数，方便控制器注入
            $extVars = [$myrequest, $myresponse];
            // $response->end($handler($vars, $extVars));
            $myresponse->setBody($handler($vars, $extVars));
            $myresponse->end();
            break;
    }
    */
});

$http->start();

/*
"autoload" : {
    "psr-4":{
        "App\\":"app/"
    }
}
composer dump-autoload
*/