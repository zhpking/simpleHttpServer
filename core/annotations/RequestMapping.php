<?php
namespace Core\annotations;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * @Annotation
 * @Target({"METHOD"})
 */
class RequestMapping {
    public $value=""; // 路径 /api/test
    public $method=[]; //方法 GET、POST
}