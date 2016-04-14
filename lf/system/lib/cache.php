<?php

namespace lf;

/**
 * This function is a shortcut to calling `(new \lf\cache)->endTimer($key)`
 * 
 * `\lf\startTimer(__METHOD__)` is shorter than `(new \lf\cache)->startTimer(__METHOD__)`
 */
function startTimer($key, $namespace = 'default')
{
	return (new \lf\cache)->startTimer($key);
}

/**
 * This function is a shortcut to calling `(new cache)-sessGet($key)`
 * 
 * `\lf\get('request')` is shorter than `(new \lf\cache)->get('request')`
 */
function endTimer($key, $namespace = 'default')
{
	return (new \lf\cache)->endTimer($key);
}

/**
 * This function is a shortcut to calling `(new cache)-sessGet($key)`
 * 
 * `\lf\get('request')` is shorter than `(new \lf\cache)->get('request')`
 */
function get($key, $namespace = 'default')
{
	return (new cache)->sessGet($key, $namespace);
}

/**
 * This function is a shortcut to calling `(new cache)-sessSet($key, $value)`
 * 
 * `\lf\set('request', $value)` is shorter than `(new \lf\cache)->set('request', $value)`
 */
function set($key, $value, $namespace = 'default')
{
	return (new cache)->sessSet($key, $value, $namespace);
}

/**
 * retrieve or set $_POST key. Cuz typing $_POST is too hard.
 */
function post($key, $value = NULL)
{
	if( !is_null($value) )		
		$_POST[$key] = $value;

	if( !isset($_POST[$key]) )
		return null;
	
	return $_POST[$key];
}

/**
 * Quick memcache style key-value pair storage into $_SESSION, just for the duration of the single request. Better than singletons, better than passing around a single object to every app
 */
class cache
{
	public function permSet($key, $value, $namespace = 'default')
	{
		$_SESSION['lf_perm'][$namespace][$key] = $value;
		return $this;
	}
	
	public function permGet($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_perm'][$namespace][$key]))
			return $_SESSION['lf_perm'][$namespace][$key];
		return NULL;
	}
	
	public function tempClearKey($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_temp'][$namespace][$key]))
			unset($_SESSION['lf_temp'][$namespace][$key]);
		return $this;
	}
	
	/**
	 * Temporarily set value into session for later user. Clears at end of PHP execution.
	 */
	public function sessSet($key, $value, $namespace = 'default')
	{
		mem::set($key, $value, $namespace);
		return $this;
	}
	
	/**
	 * Get temporary value into session for later user. Cleared at end of PHP execution.
	 */
	public function sessGet($key, $namespace = 'default')
	{
		return mem::get($key, $namespace);
	}
	
	/**
	 * Clear lf_cache $key from session
	 */
	public function sessClearKey($key, $namespace = 'default')
	{
		if(isset($_SESSION['lf_cache'][$namespace][$key]))
			unset($_SESSION['lf_cache'][$namespace][$key]);
		return $this;
	}
	
	/**
	 * Clear full lf_cache from session
	 */
	public function sessClearAll()
	{
		if(isset($_SESSION['lf_cache']))
			unset($_SESSION['lf_cache']);
		return $this;
	}
	
	/**
	 * Return given $filename content as string
	 */
	public function readFile($filename)
	{
		ob_start();
		readFile(LF.'cache/'.$filename);
		return ob_get_clean();
	}
	
	/**
	 * Save $data to $filename
	 */
	public function toFile($data, $filename)
	{
		return file_put_contents($filename, $data);
	}
	
	/**
	 * Start timer for $key 
	 */
	public function startTimer($key)
	{
		$this->sessSet($key, microtime(true), 'timer_start');
		return $this;
	}
	
	/**
	 * End timer for $key
	 */
	public function endTimer($key)
	{
		$startTime = $this->sessGet($key, 'timer_start');
		if(is_null($startTime)) return null;
		$this->sessSet($key, microtime(true) - $startTime, 'timer_result');
		return $this;
	}
	
	public function sessGetNamespace($namespace)
	{
		return $_SESSION['lf_cache'][$namespace];
	}
	
	/**
	 * Return $key from timer_result
	 */
	public function getTimerResult($key)
	{
		return $this->sessGet($key, 'timer_result');
	}
	
	/**
	 * Return timer_results in order
	 */
	public function getTimerResults()
	{
		return array_merge($this->sessGetNamespace('timer_start'), $this->sessGetNamespace('timer_result'));
	}
}

/**
 * Class just to destruct at end of PHP execution
 */
class cacheDestroyer
{
	public function __destruct()
	{
		(new \lf\cache)->sessClearAll();
	}
}

// this object will __destruct and clear the session before session save that occurs at the end of PHP execution.
$sessionDestroyer = new cacheDestroyer();