<?php

namespace Demo\test;

class Factory
{

	static public $instance = [
		'Baidu' => '\Demo\test\Baidu',
	];

	public static function getInstance($ClassName)
	{
		if (isset($class[$ClassName])) return $class[$ClassName];
		return $class[$ClassName] = new self::$instance[$ClassName]();
	}
}
