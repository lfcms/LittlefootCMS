<?php

namespace lf;

// shortcut function to retrieve the session data saved by request
// eg, `\lf\www("Index");` returns 'http://www.domain.com/littlefoot/index.php/'
function requestGet($methodSuffix)
{
	if( $methodSuffix == 'IndexUlr')
	{
		lfbacktrace();
		exit();
	}
	$method = 'get'.$methodSuffix;
	return (new \lf\request)->load()->$method();
}

// did they ask for admin
function isAdmin()
{
	return (new \lf\request)->load()->isAdmin();
}

function resolveAppUrl($html)
{
	return str_replace('%appurl%', requestGet('ActionUrl'), $html);
}

/**
 * Parses $_SERVER['REQUEST_URI'] into usable parts, generates a fake REQUEST_URI, etc if it is not set.
 * 
 * 
 * 
 * * Action is between Cwd and Param. 
 * * Action takes/gives from/to Cwd and takes/gives from/to Param. 
 * * Action is the only one that needs to be moving stuff around.
 * 
 * Check it out:
 * 
 * `<?php pre( (new \lf\request)->load()->actionPush() ); ?>`
 * 
 * 
 */
class request
{
	// store the resulting peices, default to localhost
	private $pieces = [
		'protocol' => 'http://',
		'domain' => 'localhost',
		'action' => []
	];
	
	public function rewriteOn()
	{
		$this->pieces['index'] = '';
		return $this;
	}
	
	public function rewriteOff()
	{
		$this->pieces['index'] = 'index.php/';
		return $this;
	}
	
	
	/** Parse REQUEST_URI into `$pieces` */
	public function parse($uri = null)
	{
		startTimer(__METHOD__);
		
		// if a $uri was not provided,
		if( is_null( $uri ) )
			// set as [REQUEST_URI](http://stackoverflow.com/a/4730834)
			$uri = $_SERVER['REQUEST_URI'];
		
		// [ty Anoop K](http://stackoverflow.com/a/12364085)
	    $protocol = (
			( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) 
			|| $_SERVER['SERVER_PORT'] == 443) 
				? "https://" 
				: "http://";
				
		// detect file being used as base (for API)
		$filename = 'index.php';
		if(preg_match('/^(.*)\/([^\/]+\.php)$/', $_SERVER['SCRIPT_NAME'], $match))
			$filename = $match[2];
		
		// Extract subdir
		$pos = strpos($_SERVER['SCRIPT_NAME'], $filename);
		$subdir = $pos != 0 ? substr($_SERVER['SCRIPT_NAME'], 1, $pos-1) : '/';
		
		// ignore anything to do with $_GET
		$uri = explode('?', $_SERVER['REQUEST_URI']);
		$uri = $uri[0];
		
		// if the last character in the REQUEST_URI is not `/`,
		if(substr($uri, -1) != '/')
			// Add a trailing slash
			$uri .= '/'; 
		
		// Detect subdirectory, use of index.php, request of admin, and action
		// beginning regex delimiter
		$urlregex = '/'.
			// match the subdir, dont capture
			'^\/'.str_replace('/', '\/', $subdir).	
			// figure out what the user is calling their index.php
			'(.+.php\/)?'.
			// detect if request involves admin/ access
			'(admin\/)?'.	
			// capture the rest of the string, this is the "action" by default
			'(.*)'.			
			// end regex delimiter, ignore $_GET variables
			'\??/';		
		preg_match($urlregex, $uri, $match);
		
		// was /index.php/ used? or did they use rewrite (eg, /blog/12-my-post)
		$index = $match[1];
		
		// was admin requested?
		$admin = $match[2] == 'admin/' ? true : false;
		
		// action is special because it is an array of the remaining REQUEST_URI delimited by `/`
		$action = explode('/', $match[3], -1);
		
		// If the action array has no elements,
		if( $action == [] ) 
			// Set first action as alias '' (empty string)
			$action[] = '';
		
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
		
		if( !isset($_SERVER['HTTP_HOST']) )
			$this->fakeServerGlobal();
		
		$this->pieces = [
			'protocol' 	=> $protocol,
			'domain' 	=> $_SERVER['HTTP_HOST'],
			'port' 		=> $port,
			'subdir' 	=> $subdir,
			'index' 	=> $index,
			'admin' 	=> $admin,
			'cwd'		=> [],
			'action'	=> $action,
			'param'		=> [],
			'get'		=> $_GET,
			'post'		=> $_POST
		];
		
		//$this->hook_run('post lf request');
		
		endTimer(__METHOD__);
		
		return $this;
	}
	
	
	/**
	 * Save pieces to session for later `load()`
	 */
	public function save()
	{
		// save pieces to SESSION
		set('requestPieces', $this->pieces);
		
		return $this;
	}
	
	/**
	 * Load pieces from session, parse if null then save to session, return $this
	 */
	public function load()
	{
		// load pieces from session
		$this->pieces = get('requestPieces');
		
		// idk if I should catch and recover this way...
		// is JIT ok here?
		if( is_null( $this->pieces ) )
			$this->parse()->save();
		
		return $this;
	}
	
	/**
	 * Return private `pieces`, but you should avoid relying on this.
	 */
	public function getPieces()
	{
		return $this->pieces;
	}
	
	/**
	 * Return `cwd` array piece
	 */
	public function getCwd()
	{
		return $this->pieces['cwd'];
	}
	
	/**
	 * Return `action` array piece
	 */
	public function getAction()
	{
		return $this->pieces['action'];
	}
	
	/**
	 * Return `param` array piece
	 */
	public function getParam()
	{
		return $this->pieces['param'];
	}
		
		
		
		
	/**
	 * Return `domain` string. eg, `www.domain.com`
	 */
	public function getDomain()
	{
		return $this->pieces['domain'];
	}
	
	/**
	 * Return string of full URL to subdir `lf/` is installed under.
	 * 
	 * eg, `http://www.domain.com/littlefoot/`
	 */
	public function getSubdirUrl()
	{
		extract($this->pieces);
		return $protocol.$domain.$port.'/'.$subdir;
	}
	
	/**
	 * Return string of full public URL to the `lf/` folder
	 * 
	 * eg, `http://www.domain.com/littlefoot/lf/`
	 */
	public function getLfUrl()
	{
		return $this->getSubdirUrl().'lf/';
	}
	
	/**
	 * Return string of `subdir/(index.php/)?` depending on if rewrite setting is enabled
	 * 
	 * eg, `http://www.domain.com/littlefoot/index.php/`
	 */
	public function getIndexUrl()
	{
		return $this->getSubdirUrl().$this->pieces['index'];
	}
	
	/**
	 * Return string of `subdir/(index.php/)?admin/`
	 * 
	 * eg, `http://www.domain.com/littlefoot/index.php/admin/`
	 */
	public function getAdminUrl()
	{
		return $this->getIndexUrl().'admin/';
	}
	
	/**
	 * Return string of `subdir/(index.php/)?action[0]/action[1]/.../action[n]/`
	 * 
	 * eg, `http://www.domain.com/littlefoot/index.php/blog/`
	 */
	public function getActionUrl()
	{
		// make an array without surrounding / delimiter
		$parts = array();
		
		if( $this->isAdmin() )
			$parts[] = 'admin';
		
		if( $this->pieces['cwd'] != [] )
			$parts[] = implode('/', $this->pieces['cwd'] );
		
		if( $this->pieces['action'] != [] )
			$parts[] = implode('/', $this->pieces['action'] );
		
		// implode everything with / delimiter
		return $this->getIndexUrl().implode('/', $parts ).'/';
	}
	
	public function fakeServerGlobal($requestUri = '/')
	{
		$_SERVER['HTTPS'] = 'off';
		$_SERVER['HTTP_HOST'] = 'fake.domain.com';
		$_SERVER['SERVER_PORT'] = '80';
		$_SERVER['REQUEST_URI'] = $requestUri;
		$_SERVER['SCRIPT_NAME'] = '/home/fake/public_html/index.php';
		return $this;
	}
	
	// public function forceUrl($url = null)
	// {
		// // not in use...
		// return
		
		
		// // redirect to URL specified in 'force_url' setting if not already being accessed that way
	    // if( isset($this->settings['force_url']) 
			// && $this->settings['force_url'] != '' )
		// {
			// $relbase = preg_replace('/index.php.*/', '', $_SERVER['PHP_SELF']);
			// $request = $_SERVER['HTTP_HOST'].$relbase;
			
			// $compare = preg_replace('/^https?:\/\//', '', $this->settings['force_url']);

			// if($request != $compare)
			// {
				// $redirect = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				// redirect302($this->protocol.$redirect);
			// }
		// }
	// }
	
	// take first param, set as last action, repeat $count times
	public function paramShift($count = 1)
	{
		// If there are less params than you asked for, 
		if( count($this->pieces['param']) < $count )
		{
			$this->error[] = 'Dont have '.$count.' in "param" for that';
			// you get NULL
			return NULL;
		}
		
		// Loop $count times (decrement-- until zero)
		while($count--)
		{
			// push first param to end of action. "Push one or more elements onto the end of array"
			array_push( 
				$this->pieces['action'], 
				// shift last param off: "Shift an element off the beginning of array"
				array_shift( $this->pieces['param'] ) 
			);
		}
		
		return $this;
	}
	
	// pop last element off action, into beginning of param
	// return bool indicates success 1 or fail 0
	public function actionPop($count = 1)
	{
		if( count($this->pieces['action']) == 0 )
			return NULL;
		
		// Loop $count times (decrement-- until zero)
		while($count--)
			// unshift: "Prepend one or more elements to the beginning of an array"
			array_unshift( 
				$this->pieces['param'], 
				// pop: "Pop the element off the end of array"
				array_pop( $this->pieces['action'] ) 
			);
			
		return $this;
	}
	
	// pop last element off action, into beginning of param
	// return bool indicates success 1 or fail 0
	public function actionDrop($count = 1)
	{
		if( count($this->pieces['action']) == 0 )
			return NULL;
		
		while($count--)
			$this->pieces['cwd'][] = array_shift( $this->pieces['action'] );
		
		return $this;
	}
	
	// pop last element off action, into beginning of param
	// return bool indicates success 1 or fail 0
	public function actionCount()
	{
		return count( $this->pieces['action'] );
	}
	
	// pop last element off action, into beginning of param
	// return bool indicates success 1 or fail 0
	public function actionUndrop($count = 1)
	{
		if( count($this->pieces['cwd']) < 0 )
			return NULL;
		
		while($count--)
		{
			// take last of cwd, add to front of action.
			array_unshift( $this->pieces['action'], end( $this->pieces['cwd'] ) );
			array_pop( $this->pieces['cwd'] );
		}
		
		return $this;
	}
	
	// pop all remaining action items into param
	public function fullActionPop()
	{
		$this->actionPop( $this->actionCount() );
		return $this;
	}
	
	/** I don't like the name. but this pushes all action to param, and pulls back $count */
	public function actionKeep($count = 1)
	{
		return $this
			->fullActionPop()
			->paramShift($count)
			->save(); // I kinda do want to put this at the end of all cwd/action/param shifting
	}
	
	public function isAdmin()
	{
		// could have just returned ->admin, but this must be boolean
		return $this->pieces['admin'];
	}
	
	/*public function parseUri($uri = 'todo')
	{
		startTimer(__METHOD__);
		
		if( !isset($_SERVER['HTTP_HOST']) )
			$this->fakeServerGlobal();
		// this doesnt work here, thinking about a hook class
		//$this->hook_run('pre lf request');
		
		// Assign default request values
//		$this->select['template'] = $this->settings['default_skin'];
//		$this->select['title'] = 'LFCMS';
//		$this->select['alias'] = '404';
		
		// ty Anoop K [ http://stackoverflow.com/questions/4503135/php-get-site-url-protocol-http-vs-https ]
	    $this->wwwProtocol = (
			( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ) 
			|| $_SERVER['SERVER_PORT'] == 443) 
				? "https://" 
				: "http://";
				
		$protocol = $this->wwwProtocol;
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
		
		// LEGACY
		// www.domain.com
		$this->wwwDomain = $_SERVER['HTTP_HOST'];
		
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
		$this->pieces['action'] = explode('/', $action, -1);
		if(count($this->pieces['action']) < 1) // If the action array has no elements,
			$this->pieces['action'][] = '';	 // Set first action as alias '' (empty string)
		
		//$this->hook_run('post lf request');
		
		(new \lf\cache)->endTimer(__METHOD__);
		
		return $this;
	}*/
}

/** manage request session */
// class RequestSession
// {
	// public $request = NULL;
	
	// public function __construct()
	// {
		// $this->initRequest();
	// }
	
	// /** Initialize request object, try from session, otherwise put a one into session and use that */
	// private function initRequest()
	// {
		// $this->request = get('request');
		
		// if( is_null( $this->request ) )
		// {
			// $this->request = (new \lf\request)->parseUri();
			// $this->save();
		// }
		
		// return $this;
	// }
	
	// // 
	// public function getRequest()
	// {
		// return $this->request;
	// }
	
	// public function set($request)
	// {
		// $this->request = $request;
	// }
	
	// public function save()
	// {
		// set('request', $this->request);
	// }
	
	// // relay everything else to request session
	// public function __call($method, $args)
	// {
		
		// if( !is_callable( array( (new request), $method), true, $callable_name) )
			// return null;
		
		// $result = $this->request->$method($args);
		
		
		// // any local changes need to be published to session
		// $this->save();
		
		// return $result;
	// }
// }

// My nasty solution to ensuring $_SESSION['db'] is cleared
// while allowing the orm class to use it without
class ___LastSay2
{
	public function __destruct()
	{
		if( !is_null( (new \lf\cache)->sessGet('request') ) )
		{
			(new \lf\cache)->sessClearKey('request');
		}
	}
}
$varNameDoesntMatterSoLongAsItDestructsAfterTheScriptEnds444 = new ___LastSay2();