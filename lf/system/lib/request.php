<?php

namespace lf;

class request
{
	/**  */ 
	public $protocol = 'http://';
	
	public function fakeServerGlobal($requestUri = '/')
	{
		$_SERVER['HTTPS'] = 'off';
		$_SERVER['HTTP_HOST'] = 'fake.domain.com';
		$_SERVER['SERVER_PORT'] = '80';
		$_SERVER['REQUEST_URI'] = $requestUri;
		$_SERVER['SCRIPT_NAME'] = '/home/fake/public_html/index.php';
		return $this;
	}
	
	public function forceUrl($url = null)
	{
		// redirect to URL specified in 'force_url' setting if not already being accessed that way
	    if( isset($this->settings['force_url']) 
			&& $this->settings['force_url'] != '' )
		{
			$relbase = preg_replace('/index.php.*/', '', $_SERVER['PHP_SELF']);
			$request = $_SERVER['HTTP_HOST'].$relbase;
			
			$compare = preg_replace('/^https?:\/\//', '', $this->settings['force_url']);

			if($request != $compare)
			{
				$redirect = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				redirect302($this->protocol.$redirect);
			}
		}
	}
	
	public function toSession()
	{
		(new \lf\cache)->sessSet('request', $this);
		return $this;
	}
	
	public function parseUri($uri = 'todo')
	{
		(new \lf\cache)->startTimer(__METHOD__);
		
		// this doesnt work here, thinking about a hook class
		//$this->hook_run('pre lf request');
		
		// Assign default request values
//		$this->select['template'] = $this->settings['default_skin'];
//		$this->select['title'] = 'LFCMS';
//		$this->select['alias'] = '404';
		
		// ty Anoop K [ http://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https ]
	    $this->protocol = (
			( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) 
			|| $_SERVER['SERVER_PORT'] == 443) 
				? "https://" 
				: "http://";
		
		// test Force URL (this needs to go to littlefoot())
		//$this->forceUrl();

		// detect file being used as base (for API)
		$filename = 'index.php';
		if(preg_match('/^(.*)\/([^\/]+\.php)$/', $_SERVER['SCRIPT_NAME'], $match))
			$filename = $match[2];
		
		// Extract subdir
		$pos = strpos($_SERVER['SCRIPT_NAME'], $filename);
		$subdir = $pos != 0 ? substr($_SERVER['SCRIPT_NAME'], 1, $pos-1) : '/';
		
		// Break up request URI on ? and extract GET request
		$url = explode('?', $_SERVER['REQUEST_URI'], 2);
		if(substr($url[0], -1) != '/')
			$url[0] .= '/'; //Force trailing slash
		
		if(!isset($url[1])) $url[1] = '';
		
		$this->get = $_GET;
		$this->post = $_POST;
		$this->rawGet = $url[0];
		$this->rawQuery = $url[1];
		
		// Detect subdirectory, use of index.php, request of admin, other URI variables and the GET request
		$urlregex = '/'. 	// beginning regex delimiter
			'^(\/'.str_replace('/', '\/', $subdir).')'.	// match the subdir
			'(.+.php\/)?'.	// figure out what the user is calling their index.php
			'(admin\/)?'.	// detect if request involves admin/ access
			'(.*)'.			// capture the rest of the string, this is the "action" by default
			'/'; 			// end regex delimiter
		preg_match($urlregex, $url[0], $request);
		
		// Simplify request matches
		$subdir = $request[1];
		$index  = $request[2];
		$admin  = $request[3];
		$action = $request[4];
		
		// set admin boolean from regex result
		$this->admin = $admin == 'admin/' ? true : false;
		
		// Add in 302 to fix rewrite and prevent duplicate content
		$fixrewrite = false;
		if($this->settings['rewrite'] == 'on')
		{
			if($index == 'index.php/') 
				$fixrewrite = true;
			$index = '';
		}
		if($this->settings['rewrite'] == 'off')
		{
			if($index == '') 
				$fixrewrite = true;
			$index = $filename.'/';
		}
		
		// set port if non-standard 80 and 443
		if($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443)
			$port = ':'.$_SERVER['SERVER_PORT']; 
		else 
			$port = '';
		
		// determine if we are https:// or not, set protocol
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
			$protocol = 'https://';
		else
			$protocol = 'http://';
		
		// www.domain.com
		$this->domain = $_SERVER['HTTP_HOST'];
		
		// http://www.domain.com/littlefoot/
		$this->wwwInstall 	= $protocol.$_SERVER['HTTP_HOST'].$subdir;
		
		// http://www.domain.com/littlefoot/lf/
		$this->wwwLF 	= $protocol.$_SERVER['HTTP_HOST'].$subdir.'lf/';
		
		// http://www.domain.com/littlefoot/index.php/
		$this->wwwIndex 	= $protocol.$_SERVER['HTTP_HOST'].$subdir.$index;
		
		// http://www.domain.com/littlefoot/index.php/admin/
		$this->wwwAdmin		= $this->wwwIndex.'admin/';
		
		// If rewrite needed fixed, this will redirect to the proper location given the request.
		if($fixrewrite) 
			redirect302($this->wwwIndex.$admin.$this->action);
		
		// explode the remaining URL component to see what was requested, delimiting on '/'
		$this->action = explode('/', $action, -1);
		if(count($this->action) < 1) // If the action array has no elements,
			$this->action[] = '';	 // Set first action as alias '' (empty string)
		
		//$this->hook_run('post lf request');
		//$this->endTimer(__METHOD__);
		
		// Backward compatible, dont use these.
		// They are only still hear cuz my old apps still use these :P
		$this->base = $protocol.$_SERVER['HTTP_HOST'].$subdir.$index;
		$this->baseurl = $this->base; // keep $Xurl usage
		$this->relbase = $subdir; // /subdir/ for use with web relative file reference
		$this->basenoget = $this->base.$admin.$action;
		
		(new \lf\cache)->endTimer(__METHOD__);
		
		
		
		return $this;
	}
}