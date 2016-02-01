<?php

namespace lf;

// `\lf\get('request')` is way better than `(new \lf\cache)->get('request')->getStuff`
function get($key, $namespace = 'default')
{
	return (new cache)->sessGet($key, $namespace);
}

// `\lf\set('request', $value)` is way better
function set($key, $value, $namespace = 'default')
{
	return (new cache)->sessSet($key, $value, $namespace);
}

/**
 * Quick memcache style key-value pair storage into $_SESSION, just for the duration of the single request. Better than singletons, better than passing around a single object to every app
 * 
 * 
 * 
 * 
 */
class cache
{
	// so I did this, but idk how I feel about it. since you can just use $_SESSION directly
	public function tempSet($key, $value, $namespace = 'default')
	{
		$_SESSION['lf_temp'][$namespace][$key] = $value;
		return $this;
	}
	
	public function tempGet($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_temp'][$namespace][$key]))
			return $_SESSION['lf_temp'][$namespace][$key];
		return NULL;
	}
	
	public function tempClearKey($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_temp'][$namespace][$key]))
			unset($_SESSION['lf_temp'][$namespace][$key]);
		return $this;
	}
	
	// kinda want to change this to tempSet(), and clear only temp, and leave sess as a more permanent save to session that isnt clear at end of page load
	public function sessSet($key, $value, $namespace = 'default')
	{
		$_SESSION['lf_cache'][$namespace][$key] = $value;
		return $this;
	}
	
	public function sessGet($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_cache'][$namespace][$key]))
			return $_SESSION['lf_cache'][$namespace][$key];
		return NULL;
	}
	
	public function sessClearKey($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_cache'][$namespace][$key]))
			unset($_SESSION['lf_cache'][$namespace][$key]);
		return $this;
	}
	
	public function sessClearAll()
	{
		if(isset($_SESSION['lf_cache']))
			unset($_SESSION['lf_cache']);
		return $this;
	}
	
	public function readFile($filename)
	{
		ob_start();
		readFile(LF.'cache/'.$filename);
		return ob_get_clean();
	}
	
	// files
	public function toFile($data, $filename)
	{
		return file_put_contents($filename, $data);
	}
	
	// timers
	public function startTimer($key)
	{
		$this->sessSet($key, microtime(true), 'timer_start');
		return $this;
	}
	
	public function endTimer($key)
	{
		$startTime = $this->sessGet($key, 'timer_start');
		if(is_null($startTime)) return null;
		$this->sessSet($key, microtime(true) - $startTime, 'timer_result');
		return $this;
	}
	
	public function getTimerResult($key)
	{
		return $this->sessGet($key, 'timer_result');
	}
}