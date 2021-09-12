<?php
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/app/config/define.php";

use Core\server\HttpServer;
if ($argc == 2) {
    $cmd = $argv[1];
    if ($cmd == "start") {
        $http = new HttpServer();
        $http->run();
    } elseif ($cmd == "stop") {
        // 获取当前程序运行的master_pid
        $getPid = intval(file_get_contents("./simpleHttpSwoole"));
        if ($getPid && trim($getPid) != 0) {
            \Swoole\Process::kill($getPid);
        }
    } else {
        echo "无效命令" . PHP_EOL;
    }
}