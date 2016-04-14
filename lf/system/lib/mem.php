<?php

namespace lf;

class mem
{
	private static $memory = [];
	
	public static function set($key, $value, $namespace = 'default')
	{
		return self::$memory[$namespace][$key] = $value;
	}
	
	public static function get($key, $namespace = 'default')
	{
		if( isset( self::$memory[$namespace][$key] ) )
			return self::$memory[$namespace][$key];
		else
			return NULL;
	}
}