<?php
namespace Core\init;
use Core\helper\FileHelper;
use Swoole\Process;

// 自动更新测试文件
class TestProcess {
    private $md5file;
    public function run() {
        return new Process(function() {
           while (true) {
               sleep(3);
               /*
               $files = glob(__DIR__ . "/../../*.php");
               $md5Arr = [];
               foreach ($files as $file) {
                   $md5Arr[] = md5_file($file);
               }

               $md5Value = md5(implode("", $md5Arr));
               */

               $md5Value = FileHelper::getFileMd5(ROOT_PATH . "/app/*", "/app/config");
               if ($this->md5file == "") {
                   $this->md5file = $md5Value;
                   continue;
               }

               // if (strcmp($this->>md5file, $md5Value) !== false) {
               if ($this->md5file != $md5Value) {
                   // echo "文件被改动了" . PHP_EOL;
                   echo "reloading..." . PHP_EOL;
                   $getPid = intval(file_get_contents("./simpleHttpSwoole"));
                   Process::kill($getPid, SIGUSR1);
                   $this->md5file = $md5Value;
                   echo "reloaded..." . PHP_EOL;
               }
           }
        });
    }
}