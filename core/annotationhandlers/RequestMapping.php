<?php
namespace Core\annotationHandlers;
use Core\annotations\RequestMapping;
use Core\BeanFactory;

return [
    RequestMapping::class => function(\ReflectionMethod $method, $instance, $self) {
        $path = $self->value; //uri
        $requestMethod = count($self->method) > 0?$self->method:["GET"];
        $routerCollector = BeanFactory::getBean("RouterCollector");

        $routerCollector->addRouter($requestMethod, $path, function () use ($method, $instance) {
            return $method->invoke($instance);
        });

        return $instance;
    }
];