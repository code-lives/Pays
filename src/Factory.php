<?php

namespace Applet\Pay;

class Factory
{
    public static $instance = [
        'Baidu' => '\Applet\Pay\Baidu',
        'Byte' => '\Applet\Pay\Byte',
        'Weixin' => '\Applet\Pay\Weixin',
        'Kuaishou' => '\Applet\Pay\Kuaishou',
        'Ali' => '\Applet\Pay\Ali',
    ];

    public static function getInstance($ClassName)
    {
        static $class;
        if (isset($class[$ClassName])) {
            return $class[$ClassName];
        }
        return $class[$ClassName] = new self::$instance[$ClassName]();
    }
}
