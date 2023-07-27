<?php

namespace Applet\Pay;

/**
 * @method static \Applet\Assemble\Ali Ali()
 * @method static \Applet\Assemble\Kuaishou Kuaishou()
 * @method static \Applet\Assemble\Byte Byte()
 * @method static \Applet\Assemble\Baidu Baidu()
 * @method static \Applet\Assemble\Weixin Weixin()
 */

class Factory
{
    public static $instance = [
        'Baidu' => '\Applet\Assemble\Baidu',
        'Byte' => '\Applet\Assemble\Byte',
        'Weixin' => '\Applet\Assemble\Weixin',
        'Kuaishou' => '\Applet\Assemble\Kuaishou',
        'Ali' => '\Applet\Assemble\Ali',
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
