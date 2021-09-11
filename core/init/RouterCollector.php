<?php
namespace Core\init;
use Core\annotations\Bean;

/**
 * @Bean()
 */
class RouterCollector {
    public $routes = [];
    // 收集路由
    public function addRouter($method, $uri, $handler) {
        $this->routes[] = ["method" => $method, "uri" => $uri, "handler" => $handler];
    }

    public function getDispatcher() {
        return \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
            /*
            $r->addRoute("GET","/test",function(){
                // return "my test";
                return json_encode(array("bid" => 10000200275));
            });

            $r->addRoute("GET","/",function(){
                return "index test";
            });
            */

            foreach ($this->routes as $route) {
                $r->addRoute($route["method"], $route["uri"], $route["handler"]);
            }
        });
    }
}