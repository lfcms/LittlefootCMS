<?php

/**
 * A base class definition meant to be extended in littlefoot apps
 *
 * # How to App
 *
 * When you `extend app` brings a lot of helpful tools into your environment.
 * Here is a list of what is accessible when developing a littlefoot app. All that follows is accessible in the app's index.php as well.
 *
 * ## $this->
 * 
 * There are several objects available in your environment:
 * 
 * * **$this->lf**: [Littlefoot](http://littlefootcms.com/files/docs/classes/Littlefoot.html) instance
 * 	* ->appurl (rendering of %appurl% without waiting for $lf->render(). Ideal for use with [redirect302();](http://littlefootcms.com/files/docs/index.html#method_redirect302) )
 * 	* ->baseurl (%baseurl%)
 * 	* ->relbase (%relbase%)
 * * **$this->db**: [Database](http://littlefootcms.com/files/docs/classes/Database.html) instance
 * * **$this->auth**: [Auth](http://littlefootcms.com/files/docs/classes/auth.html) instance
 * 
 * ## Static
 * 
 * Classes meant to be called statically:
 * 
 * * [**orm**]((http://littlefootcms.com/files/docs/classes/orm.html#method_q))::q('table_name')
 * * [**dba**](#)::Table_name
 *
 * ## init()
 * 
 * If you need something to run before all functions, you can do it in an 'init()' function. This function is automatically called from __construct() just before any other controller is executed.
 *
 * ## $args
 * 
 * Given `domain.com/myapp/view/5`
 * 
 * ~~~
 * $this->lf->action = array('myapp');
 * $this->lf->vars = array('view', '5');
 * ~~~
 * 
 * Littlefoot serves requests based on the URL. For example, if we ask for "domain.com/blog", it knows to serve "/blog". The "/blog" navigation item has been associated with the "Blog" app in the admin backend. So we wind up being presented with Blog. Simple enough right? Now lets get to $args.

 * The Blog navigation item has been set as an "app". This means that any extra part of the URL past the matching "/blog" request is taken as variables for the "Blog" app rather than a separate navigation item. Alternatively, if it was not set as an "app", we could make child navigation items and serve them without conflict.

 * If we ask for "domain.com/blog/view/5", the "view/5" is taken as a variable for the Blog app. The Blog app utilizes a Model-View-Controller structure and uses its class methods as a kind of router. The variable taken from the url is split on the "/" to give us an array of "(view, 5)". The first part, "view", is used to determine which method of Blog to serve. In this case, it will use the view() method and since the "5" is specified, the Blog is programmed to serve the Blog post with an "id" of 5.
 *
 * ## MVC
 *
 * The URI is chopped up into arguments and those arguments route the controller. 
 * 
 * ### URI routing to controller class methods
 * 
 * Create a file at `ROOT/apps/myapp/index.php` that contains
 * ~~~
 * <?php echo $this->lf->mvc('myapp');
 * ~~~
 * 
 * Then at `ROOT/apps/myapp/controller/myapp.php`, create a file with the following code:
 * 
 * ~~~
 * <?php
 *
 * class myapp extends app {
 *		function main($args) {
 *			echo '<a href="%appurl%otherfunction/1">otherfunction</a>';
 * 			//echo '<pre>';
 *			//var_dump($args, $this);
 *			//echo '</pre>';
 * 		}
 *
 *		function otherfunction($args) {
 *			echo '<a href="%appurl%">back to main</a>';
 *			echo '<pre>';
 *			var_dump($args);
 *			echo '</pre>';
 * 		}
 * }
 * ~~~
 *
 * If you go into the [Dashboard](http://littlefootcms.com/byid/24) and assign this app to the navigation, a link will appear on the navigation. When you click it, the screen will display the content of $args 
 * 
 * I strongly recommend reviewing the code of the [Pages](https://github.com/bioshazard/pages) app. It is an excellent example of the intended use of `mvc()`
 * 
 * With the above app assigned in nav at `/theapp/`, the above renders as follows:
 * 
 * ~~~
 * <a href="http://domain.com/theapp/otherfunction">otherfunction</a>
 * ~~~
 * 
 * If you click this link, the app will render as follows:
 * 
 * ~~~
 * <a href="http://domain.com/theapp/">main</a>
 * <pre>
 * array(0 => 'otherfunction', 1 => '1')
 * </pre>
 * ~~~
 * 
 * And links back to the main function. Follow the [Littlefoot app tutorial]() for a more in-depth guide.
 * 
 * ## Variable Scope
 * 
 * If you need a value to be accessible throughout an app (including within a partial) without needing to pass by value or reference, you can simply set the variable as such:
 * 
 * `$this->mySpecialVar = 'some value I want to use everywhere, or just for this part';`
 * `$this->myOtherSpecialVar = array('something' => 'cool');`
 * 
 * And these will be accessible everywhere in the app (helpful for recursive partials)
 * 
 * ## Partials
 * 
 * $this->partial('some-partial', array('myvar' => 'someval');
 * 
 * ```
 * <?php // view/some-partial.php
 * 
 * echo $myvar; // "someval"
 * ```
 * 
 */
class app
{
	/** @var Database Datbase wrapper accessible via $this->db */
	public $db;
	
	/** @var string Configuration data set in the navigation configuration. This data is saved to the lf_actions table. */
	protected $ini;
	
	/** @var Littlefoot For backword compatibility. Synonymous with $this->lf */
	protected $request;
	
	/** @var Littlefoot Littlefoot instance: Access to URL variables, mvc(), etc */
	protected $lf;
	
	/** @var auth Auth object. Access to access data (username, id, etc) */
	protected $auth;
	
	/** @var default_method Used to specify the default method when none is specified. This is set to 'main' by default. */
	public $default_method = 'main';
	
	/**
	 * Initializes the app environment. For use with $this->lf->mvc() routing.
	 * 
	 * @param Littlefoot $lf The Littlefoot instance. Accessible at **$this->lf**
	 * 
	 * @param Database $dbconn Database wrapper. Accessible at **$this->db**
	 * 
	 * @param string $ini Configured ini value in `lf_actions` table. Accessible at **$this->ini**
	 *
	 * @param array $args URL Variables. Accessible at **$this->args**
	 */
	public function __construct($lf, $ini = '', $args = array())
	{
		$this->db = db::init();
		$this->request = $lf; // backward compatible
		$this->lf = $lf->lf->lf->lf; // lol recursion
		$this->auth = $lf->auth_obj;
		$this->ini = $ini;
		$this->args = $args;
		
		// so you can run things on construct without re-making it
		if(method_exists($this, 'init')) $this->init($args); 
	}
	
	/**
	 * Default main() function. Should be replaced in all classes extended from app.
	 
	public function main() // = array() is for backward compatibility
	{
		echo '::default main function::';
	}*/
	
	/**
	 * Used for loading partial views given an argument
	 * 
	 * @param string $file The name of the view. Loaded from view/$file.php
	 * @param array $args Associative array of $var => $val passed to the partial.
	 */
	public function partial($partial, $args = array())
	{
		foreach($args as $var => $val)
			$$var = $val;
		
		ob_start();
		include 'view/'.$partial.'.php';
		return ob_get_clean();
	}
	
	/** 
	 * used to route based on args[0] as instance
	 *
	 * ### How to use _router
	 *
	 * Dynamically route controller based on a common URI base
	 *
	 * ie, `/_auth/mymethod`, `/_auth/myothermethod`
	 *
	 * ~~~ 
	 * $auth = new auth($this, $this->db);
	 *
	 * // change to auth class 
	 * if($this->action[0] == '_auth' && isset($this->action[1]))
	 * {
	 * 		$out = $auth->_router($this->action);
	 * 		$out = str_replace('%appurl%', $this->base.'_auth/', $out);
	 * 		$content['%content%'][] = $out;
	 * 	
	 * 		// display in skin
	 * 		echo $this->render($content);
	 * 	
	 * 		exit(); // end auth session after render, 
	 * 		// otherwise it will 302 (login/logout)
	 * }
	 * ~~~
	 *
	 * @param array $args URL Variables.
	 *
	 * @param string $default_route Default function for router when none is specified. Uses function "home" by default.
	 *
	 * @param array $filter If set, limit valid functions to those in the array; eg, array('func2', 'func3')
	 *
	 * @return string Captured output buffer from execution of $this->$method()
	
	*/
	public function _router($args, $default_route = 'home', $filter = array())
	{
		$this->instbase = $this->lf->appurl.$args[0].'/'; // url lf->appurl to all
		$this->inst = urldecode($args[0]); // can handle any string
		
		// Load 
		$args = array_slice($args, 1); // move vars over to emulate direct execution
		
		/** @var string variable used to execute method based on $default_route( or $args[0] if set) */
		$method = $default_route;
		
		// if a base variable is specified,
		if(isset($args[0])) 
			// if no filter is specified,
			if($filter == array()) 
				$method = $args[0];
			// if $filter has more than no elements and $args[0] is in the filter,
			else if(in_array($args[0], $filter)) 
				$method = $args[0];
		
		// begin output capture
		ob_start();
		
		// execute given method of $this object
		$this->$method($args);
		
		// replace appurl with instance base and return
		return str_replace('%insturl%', $this->instbase, ob_get_clean()); 
	}
	
	// ALPHA notice('some message to store in session')
	// notice() // prints the message
	public function notice($msg = '', $namespace = 'lf')
	{
		if($msg != '')
		{
			$_SESSION['notice_'.$namespace][] = $msg;
		}
		else if(isset($_SESSION['notice_'.$namespace]))
		{
			$temp = $_SESSION['notice_'.$namespace];
			unset($_SESSION['notice_'.$namespace]);
			return implode(', ', $temp);
		}
	}
	
	public function hasnotice($namespace = 'lf')
	{
		return isset($_SESSION['notice_'.$namespace]);
	}
}

?>
