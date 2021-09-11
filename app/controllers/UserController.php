<?php
namespace App\controllers;
use Core\annotations\Bean;
use Core\annotations\RequestMapping;
use Core\annotations\Value;

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
}