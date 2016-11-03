<?php

namespace lf;

/**
 * Template ssyttem
 * 
 * Littlefoot->select will go here. so will render
 * 
 * ### Dev note
 * 
 * I have found a problem with calling template asynchronously:
 * 
 * If you store it in a variable: `$template = (new template)`, and make changes to the variable, it keeps its original values. So if you append to title somewhere else, save this will revert back to the original title that hasnt been changed in this local instance. Maybe a statis internal variable would have fixed that, but I still like session better until someone talks me out of it.
 * 
 * 
 * 
 */
class template
{
	// I need to save everything into an array to push to session and get
	private $elements = [
		// page <title />, array gets imploded on ->getTitle()
			'title' => ['LittlefootCMS'],
		// to let others give some extra <head />
			'head' => [],
		// array of CSS URLs to include
			'css' => [],
		// array of JS URLs
			'js' => [],
		// template (skin) to load upon ->render()
			'skin' => 'default',
		// AJAX
			'ajax' => false,
		// use skin home.php
			'home' => false
	];
	
	/**
	 * At construct time, load elements from session
	 * 
	 * Detect if Ajax request
	 */
	public function __construct()
	{
		// i dont see a reason not to auto load from session if its there
		$this->load();
		
		// ajax mode (should maybe go into LF class)
		if( (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
		  && ( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
		  || isset($_GET['ajax'])
		  ){
	            $this->elements['ajax'] = true;
	    }
	}
	
	/**
	 * Try to get elements from session, otherwise, push default ones to session
	 */
	public function load()
	{
		// attempt to load elements already published to session
		$elements = get('templateElements');
		
		// if we find something,
		if( ! is_null( $elements ) )
			// save session elements back into local elements
			$this->elements = $elements;
		else
			// publish defaults to session, no changes needed before returning as the defaults are already set in the object
			$this->save();
		
		return $this;
	}
	
	/**
	 * Save current $this->elements to session
	 */
	private function save()
	{
		set('templateElements', $this->elements);
		return $this;
	}
	
	/**
	 * Add $content to the end of the <head /> array 
	 */
	public function addHead($content)
	{
		$this->elements['head'][] = $content;
		return $this->save();
	}
	
	/**
	 * Set whole title to $newTitle
	 */
	public function setTitle($newTitle)
	{
		$this->elements['title'] = [$newTitle];
		return $this->save();
	}
	
	/**
	 * Add $extraTitle to end of `title` array
	 */
	public function appendTitle($extraTitle)
	{
		$this->elements['title'][] = $extraTitle;
		return $this->save();
	}
	
	/**
	 * Return either the imploded array of the given $key, or an error stating that there was nothing saved at that key.
	 */
	public function printContent($key = 'content')
	{
		if(isset($this->elements['content'][$key]))
			return implode($this->elements['content'][$key]);
		else
			return 'Content not found: No such key "'.$key.'" set';
		
			/*foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);*/
	}
	
	/**
	 * Capture login output, return as string
	 */
	public function getLogin()
	{
		ob_start();
		$this->printLogin();
		return ob_get_clean();
	}
	
	/**
	 * Include login system template
	 */
	public function printLogin()
	{
		include LF.'system/template/login.php';
		return $this;
	}
	
	/**
	 * Get imploded title array around (' | ')
	 */
	public function getTitle()
	{
		return implode(' | ', $this->elements['title']);
	}
	
	/**
	 * Returns http:// URL of the folder root of the current skin
	 */
	public function getSkinBase()
	{
		return requestGet('LfUrl').'skins/'.$this->getTemplateName().'/';
	}
	
	/**
	 * Add $data to the list of output section array called $namespace ('content' by default)
	 * 
	 * This is used by the template system to later print easily into the skin.
	 */
	public function addContent($data, $namespace = 'content')
	{
		$this->elements['content'][$namespace][] = $data;
		return $this->save();
	}
	
	/**
	 * Return an implode of the array saved at $namespace. We put things in there with `$this->addContent($string, 'content');`
	 */
	public function getContent($namespace = 'content')
	{
		if( ! isset( $this->elements['content'][$namespace] ) )
			return null;
		
		return implode($this->elements['content'][$namespace]);
	}
	
	/**
	 * Add a CSS URL to the css array. We handle the HTML for it separately in `renderCss()`
	 */
	public function addCss($url)
	{
		$this->elements['css'][] = $url;
		return $this->save();
	}
	
	/**
	 * Add a JS URL to the js array. We handle this HTML for it separately in `renderJs()`
	 */
	public function addJs($url)
	{
		$this->elements['js'][] = $url;
		return $this->save();
	}
	
	/**
	 * Kyle wrote this. I like the idea of splitting the URI with `/`, but would need some reworking. I dont currently use this method.
	 */
	public function getUriAsArray(){
		$uri = $_SERVER['REDIRECT_URL'];
		//set the uriPath as string.
		$this->uriPath = $uri;
		$uri = explode('/',$uri);
		//take the first / out
		array_shift($uri);
		return $this->uri = $uri;
	}
	
	/**
	 * Render the stored content results and render anything in css, js, head after we exec the template
	 * 
	 * You can render from a different LF folder. Just chdir to it before render, and it will not know you moved somewhere. this works in the index.php as well
	 */
	public function render()
	{
		(new \lf\plugin)->run('pre template render');
		startTimer(__METHOD__);
		
		// replace head stuff into <head>
		$template = $this->replaceHead(
			// templates print their own content
			$this->execTemplate()
		);
		
		endTimer(__METHOD__);
		(new \lf\plugin)->run('post template render');
		return $template;
	}
	
	
	/**
	 * Load skin data
	 */
	public function execTemplate()
	{
		// can we use home.php for this request?
		// it lets us have a unique home page.
		$file = $this->elements['home'] && (is_file($this->getTemplatePath().'home.php') || is_file($this->getTemplatePath().'home.html'))
			? 'home'
			: 'index';
		
		ob_start();
		
		//pre($this->content);
		if(is_file($this->getTemplatePath()."$file.php")) // allow php
			include($this->getTemplatePath()."$file.php");
		else if(is_file($this->getTemplatePath()."$file.html"))
			readfile($this->getTemplatePath()."$file.html");
		else
			echo 'Template file "'.$this->getTemplatePath().$file.'.php" missing. Log into <a href="'.\lf\requestGet('AdminUrl').'">Admin</a> and select a different template with the Skins tool.';
			
		return ob_get_clean();
	}
	
	/**
	 * Replace the `renderHead()` result into the bottom of `</head>` with a string replace.
	 */
	public function replaceHead($template)
	{
		// apply head. needs to be first
		$template = str_replace(
			'<head>', 
			'<head>'.$this->renderHead(), 
			$template
		);
		
		return $template;
	}
	
	/**
	 * Combines js, css, and anything wrong in head
	 */
	public function renderHead()
	{
		return implode("\n", [
				$this->renderJs(),
				$this->renderCss(),
				implode( "\n", $this->elements['head'] )
			]);
	}
	
	/**
	 * For each js URL, render into HTML `<script />` include, and implode results over "\n"
	 */
	public function renderJs()
	{
		$parts = [];
		foreach($this->elements['js'] as $js)
		{
			$parts[] = '<script src="'.$js.'" type="text/javascript"></script>';
		}
		return implode("\n", $parts);
	}
	
	/**
	 * For each css URL, render into HTML `<link />` include, and implode results over "\n"
	 */
	public function renderCss()
	{
		$parts = [];
		foreach($this->elements['css'] as $css)
			$parts[] = '<link rel="stylesheet" href="'.$css.'" />';
		return implode("\n", $parts);
	}
	
	/**
	 * Set the home boolean. If enabled, home.php would be preferred over index.php, but will fall back to index.php if not there.
	 */
	public function setHome($bool)
	{
		$this->elements['home'] = $bool;
		return $this->save();
	}
	
	/**
	 * Return string of path to currently selected skin
	 */
	public function getTemplatePath()
	{
		if( ! isset( $this->elements['admin'] ) )
			$this->setAdmin( isAdmin() );
		
		$skinDir = $this->elements['admin']
			? 'system/admin/skin/' 
			: 'skins/';
			
		return LF.$skinDir.$this->getTemplateName().'/';
	}
	
	/**
	 * Set whether or not to load from admin skins or from public skins
	 */
	public function setAdmin($as = true)
	{
		$this->elements['admin'] = (bool) $as; // php 5.5+ has boolval()
		return $this->save();
	}
	
	/**
	 * Return name of currently selected skin
	 */
	public function getTemplateName()
	{
		return $this->elements['skin'];
	}
	
	/**
	 * Set skin with `$skinName` to load upon `render()`
	 */
	public function setSkin($skinName)
	{
		$this->elements['skin'] = $skinName;
		
		return $this->save();
	}
	
	// public function display($viewPath){
		// if($this->ajaxMode){
			// return $this->ajaxRender();
		// }
		// $this->renderHeader();
		// $this->includeScripts();
		// $this->renderNavbar();
		// $this->renderBreadcrumb();
		// $this->showMess();
		// include($viewPath);
		// $this->renderFooter();
		// unset($_POST);
		// return;
	// }
	// public static function render404(){
		// include(ROOT.'system/view/errors/404.html');
	// }
	// public function renderHeader(){
		// include($this->docRoot.'/html/template/topbar.php');
	// }
	// public function renderNavbar(){
		// include($this->docRoot.'/html/template/navigation.php');
	// }
	// public function renderFooter(){
		// include($this->docRoot.'/html/template/footer.php');
	// }
	// public function showMess(){
		// if( isset($_SESSION['messages'][0] )&& is_array($_SESSION['messages'][0]) ){
			// foreach($_SESSION['messages'] as $mess){
				// echo Message::create($mess);
			// }
		// $_SESSION['messages']=array();
		// }
	// }
	// public function includeScripts(){
		// include($this->docRoot.'/html/template/scripts.php');
	// }
	// public function includeAdminScripts(){
		// include($this->docRoot.'/html/admin/scripts.php');

	// }
	// public function renderBreadcrumb(){
		// if(!isset($this->pageName) || $this->pageName ==''){
			// $this->pageName = 'Welcome';
		// }
		// if(!isset($this->subtitle) || $this->subtitle ==''){
			// $this->subtitle = 'Door application';
		// }

		// include($this->docRoot.'/html/template/breadcrumb.php');
	// }
	// public function getNavBar(){
		// include($this->docRoot.'/config/navigation.php');
		// return $navigation;
	// }
	// public static function getSideBarWidget(){
		// return 'sidebar';
	// }
	// public function displayLogin($errorMess) {
		// $this->errorMess = $errorMess;
		// include($this->docRoot.'/html/login.php');
		// exit;
	// }
}