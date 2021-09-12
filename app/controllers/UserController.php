<?php
namespace App\controllers;
use Core\annotations\Bean;
use Core\annotations\RequestMapping;
use Core\annotations\Value;
use Core\http\Request;
use Core\http\Response;

/**
 * @Bean()
 */
class UserController {
    /**
     * @Value(name="version")
     */
    public $version;

    /**
     * @RequestMapping(value="/test",method={})
     */
    public function test() {
        return "test";
    }

    /**
     * @RequestMapping(value="/user/{uid:\d+}",method={})
     */
    public function user(int $uid, Request $request, Response $response) {
        // var_dump($request->getQueryParams());
        // return "user" . $uid;

        // var_dump(11111);
        return $request->getQueryParams();
    }
}