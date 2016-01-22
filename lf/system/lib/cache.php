<?php

namespace lf;

class cache
{
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
	
	// files
	public function toFile($data, $filename)
	{
		//todo, just use file_put_contents...
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