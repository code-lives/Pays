<?php

namespace Applet\Pay;

/**
 * @method static \Applet\Pay\Ali Ali()
 * @method static \Applet\Pay\Kuaishou Kuaishou()
 * @method static \Applet\Pay\Byte Byte()
 * @method static \Applet\Pay\Baidu Baidu()
 * @method static \Applet\Pay\Weixin Weixin()
 */
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

    public static function __callStatic($method, $args)
    {
        return self::getInstance($method, $args);
    }
}
