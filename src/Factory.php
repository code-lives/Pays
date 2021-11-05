<?php

namespace Applet\Pay;

class Factory
{

	static public $instance = [
		'Baidu' => '\Applet\Pay\Baidu',
		'Byte' => '\Applet\Pay\Byte',
		'Weixin' => '\Applet\Pay\Weixin',
	];

	public static function getInstance($ClassName)
	{
		if (isset($class[$ClassName])) return $class[$ClassName];
		return $class[$ClassName] = new self::$instance[$ClassName]();
	}
}
