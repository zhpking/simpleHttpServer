<?php
namespace Core\helper;
class FileHelper {
    static public function getFileMd5($dir, $ignore) {
        $files = glob($dir);
        $ret = [];
        foreach ($files as $file) {
            if (is_dir($file) && strpos($file, $ignore) == false) {
                // 如果是文件夹，继续递归调用
                $ret[] = self::getFileMd5($file . "/*", $ignore);
            } else if (isset(pathinfo($file)["extension"]) && pathinfo($file)["extension"] == "php") {
                $ret[] = md5_file($file);
            }
        }

        return md5(implode("", $ret));
    }
}