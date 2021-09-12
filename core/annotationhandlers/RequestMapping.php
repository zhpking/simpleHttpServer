<?php
namespace Core\annotationHandlers;
use Core\annotations\RequestMapping;
use Core\BeanFactory;

return [
    RequestMapping::class => function(\ReflectionMethod $method, $instance, $self) {
        $path = $self->value; //uri
        $requestMethod = count($self->method) > 0?$self->method:["GET"];
        $routerCollector = BeanFactory::getBean("RouterCollector");

        $routerCollector->addRouter($requestMethod, $path, function ($params, $extParams) use ($method, $instance) {
            // return $method->invoke($instance);

            $inputParams = [];
            // 获取放射方法的参数
            $refParams = $method->getParameters();
            foreach ($refParams as $refParam) {
                // 判断路由参数是否存在
                if (isset($params[$refParam->getName()])) {
                    $inputParams[] = $params[$refParam->getName()];
                } else {
                    // $extParams在index.php传入进来的都是实例对象
                    foreach ($extParams as $extParam) {
                        // 需要把实例对象注入进去放射方法的时候，需要判断参数是否为同一个对象类型
                        if ($refParam->getClass()->isInstance($extParam)) {
                            $inputParams[] = $extParam;
                            // 跳出else循环，不跳出的话$inputParams[] = false会执行然后导致参数对应错误
                            goto ForElseContinue;
                        }
                    }
                    $inputParams[] = false;
                }

                ForElseContinue:
            }

            return $method->invokeArgs($instance, $inputParams);
        });

        return $instance;
    }
];