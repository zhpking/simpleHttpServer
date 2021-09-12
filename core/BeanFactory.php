<?php
namespace Core;

use DI\ContainerBuilder;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class BeanFactory {
    static private $env = []; // env配置文件
    static private $container; // ioc容器
    static private $handlers = [];

    static public function init() {
        // 初始化配置文件
        self::$env = parse_ini_file(ROOT_PATH . "/env");
        // 初始化容器builder
        $builder = new ContainerBuilder();
        // 启用注解，主要用它的Inject注解
        $builder->useAnnotations(true);
        // 容器初始化
        self::$container = $builder->build();
        // self::$handlers = require_once (ROOT_PATH . "/core/annotations/AnnotationHandlers.php");
        $handlers = glob(ROOT_PATH . "/core/annotationhandlers/*.php");
        foreach ($handlers as $handler) {
            self::$handlers = array_merge(self::$handlers, require_once($handler));
        }

        // 设置类注解加载（类似composer里的register方法）
        $loader = require __DIR__ . "/../vendor/autoload.php";
        AnnotationRegistry::registerLoader([$loader, "loadClass"]);


        // key:从配置中获取扫描路径，value:扫描的namespace
        $scans = [
            ROOT_PATH . "/core/init" => "Core\\", // 先扫描框架内部必须要扫描的文件夹
            self::getEnv("scan_dir", ROOT_PATH . "/app") => self::getEnv("scan_root_namespace", "App\\")
        ];
        foreach ($scans as $scanDir => $scanRootNamespace) {
            // 扫描目录文件
            self::ScanBeans($scanDir, $scanRootNamespace);
        }
    }

    // 获取env文件中的配置内容
    static private function getEnv(string $key, string $default="") {
        if (isset(self::$env[$key])) {
            return self::$env[$key];
        }

        return $default;
    }

    static public function getBean($name) {
        return self::$container->get($name);
    }

    static private function getAllBeanFiles($dir) {
        // ./app
        $ret = [];
        $files = glob($dir . "/*");
        foreach ($files as $file) {
            if (is_dir($file)) {
                $ret = array_merge($ret, self::getAllBeanFiles($file));
            } else if (isset(pathinfo($file)["extension"]) && pathinfo($file)["extension"] == "php") {
                $ret[] = $file;
            }
        }

        return $ret;
    }

    static public function ScanBeans($scanDir, $scanRootNamespace) {
        // 获取注解处理方法
        // $annoHandlers = require_once (ROOT_PATH . "/core/annotations/AnnotationHandlers.php");
        $annoHandlers = self::$handlers;

        // 从配置中获取扫描路径
        // $scanDir = self::getEnv("scan_dir", ROOT_PATH . "/app");
        // 扫描的namespace
        // $scanRootNamespace = self::getEnv("scan_root_namespace", "App\\");

        /*
        $files=glob($scanDir . "/*.php");
        foreach ($files as $file) {
            require_once $file;
        }
        */

        // 多级目录扫描
        $files = self::getAllBeanFiles($scanDir);
        foreach ($files as $file) {
            require_once $file;
        }

        $reader = new AnnotationReader();
        // AnnotationRegistry::registerAutoloadNamespace("Core\annotations");

        // 实例化类
        foreach (get_declared_classes() as $class) {
            if (strstr($class, $scanRootNamespace)) {
                $refClass = new \ReflectionClass($class);
                // 获取所有类注解
                $classAnnos = $reader->getClassAnnotations($refClass);

                // 处理类方法
                foreach ($classAnnos as $classAnno) {
                    $handler = $annoHandlers[get_class($classAnno)];
                    $instance = self::$container->get($refClass->getName());
                    // todo 处理属性注解
                    self::handlerPropAnno($instance, $refClass, $reader);

                    // todo 处理方法注解
                    self::handlerMethodAnno($instance, $refClass, $reader);


                    $handler($instance, self::$container, $classAnno);
                }
            }
        }
    }

    /**
     * 处理属性注解
     * @param $instance
     * @param \ReflectionClass $ref_class
     * @param AnnotationReader $reader
     */
    static private function handlerPropAnno(&$instance, \ReflectionClass $ref_class, AnnotationReader $reader) {
        $props = $ref_class->getProperties(); // 取出反射对象所有属性
        foreach ($props as $prop) {
            // 获取属性上方的注解
            $propAnnos = $reader->getPropertyAnnotations($prop);
            foreach ($propAnnos as $propAnno) {
                $handler = self::$handlers[get_class($propAnno)];
                // 处理属性注解
                $instance = $handler($prop, $instance, $propAnno);
            }
        }
    }

    /**
     * 处理方法注解
     * @param $instance
     * @param \ReflectionClass $ref_class
     * @param AnnotationReader $reader
     */
    static private function handlerMethodAnno(&$instance, \ReflectionClass $ref_class, AnnotationReader $reader) {
        $methods = $ref_class->getMethods(); // 取出反射对象所有方法
        foreach ($methods as $method) {
            // 获取属性上方的注解
            $methodAnnos = $reader->getMethodAnnotations($method);
            foreach ($methodAnnos as $methodAnno) {
                $handler = self::$handlers[get_class($methodAnno)];
                // 处理方法注解
                $instance = $handler($method, $instance, $methodAnno);
            }
        }
    }
}