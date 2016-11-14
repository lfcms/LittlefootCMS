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
	
	public static function dump($namespace = 'default')
	{
		return self::$memory[$namespace];
	}
	
	public static function save($key, $namespace = 'default')
	{
		// TODO save to memcache, or whatever is plugged in as a key/value provider
	}
	
	public static function load($key, $namespace = 'default')
	{
		// TODO load $namespace_$key from memcache, or whatever is plugged in as a key/value provider
	}
}