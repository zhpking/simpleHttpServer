<?php

namespace Core\annotationHandlers;
use Core\annotations\Bean;
use Core\annotations\Value;

return [
    // 类注解
    Bean::class => function($instance, $container, $self) {
        $vars = get_object_vars($self);
        $beanName = "";
        if (isset($vars["name"]) && $vars["name"] != "") {
            $beanName=$vars["name"];
        } else {
            $arrs = explode("\\", get_class($instance));
            $beanName = end($arrs);
        }
        $container->set($beanName, $instance);
    },

    // 熟悉注解
    Value::class => function(\ReflectionProperty $prop, $instance, $self) {
        $env = parse_ini_file(ROOT_PATH. "/env");
        if (!isset($env[$self->name]) || $self->name == "") {
            return $instance;
        }

        $prop->setValue($instance, $env[$self->name]);
        return $instance;
    }
];