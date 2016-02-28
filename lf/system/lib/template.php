<?php

namespace lf;

/**
 * Template ssyttem
 * 
 * Littlefoot->select will go here. so will render
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
			'skin' => 'XV',
		// AJAX
			'ajax' => false,
		// use skin home.php
			'home' => false
	];
	
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
	
	public function load()
	{
		$elements = get('templateElements');
		
		// if we already have something saved, load it
		if( !is_null( $elements ) )
			$this->elements = $elements;
		
		return $this;
	}
	
	// save to session
	private function save()
	{
		set('templateElements', $this->elements);
		return $this;
	}
	
	public function addHead($content)
	{
		$this->elements['head'][] = $content;
		return $this->save();
	}
	
	// set Title
	public function setTitle($newTitle)
	{
		$this->elements['title'] = [$newTitle];
		return $this->save();
	}
	
	public function printContent($key = 'content')
	{
		if(isset($this->elements['content'][$key]))
			return implode($this->elements['content'][$key]);
		else
			return 'Content not found: No such key "'.$key.'" set';
		
			/*foreach($this->content as $key => $value)
				$template = str_replace($key, implode($value), $template);*/
	}
	
	// set Title
	public function appendTitle($extraTitle)
	{
		$this->elements['title'][] = $extraTitle;
		return $this->save();
	}
	
	public function getLogin()
	{
		ob_start();
		$this->printLogin();
		return ob_get_clean();
	}
	
	public function printLogin()
	{
		include LF.'system/template/login.php';
		return $this;
	}
	
	// get Title
	public function getTitle()
	{
		return implode(' | ', $this->elements['title']);
	}
	
	public function getSkinBase()
	{
		return requestGet('LfUrl').'skins/'.$this->getTemplateName().'/';
	}
	
	/* Content operations */
	
	// addContent
	public function addContent($data, $namespace = 'content')
	{
		$this->elements['content'][$namespace][] = $data;
		return $this->save();
	}
	
	public function getContent($namespace = 'content')
	{
		if( ! isset( $this->elements[$namespace] ) )
			return null;
		
		return implode($this->elements[$namespace]);
	}
	
	public function addCss($url)
	{
		$this->elements['css'][] = $url;
		return $this->save();
	}
	
	public function addJs($url)
	{
		$this->elements['js'][] = $url;
		return $this->save();
	}
	
	// should do this instead of the regex
	public function getUriAsArray(){
		$uri = $_SERVER['REDIRECT_URL'];
		//set the uriPath as string.
		$this->uriPath = $uri;
		$uri = explode('/',$uri);
		//take the first / out
		array_shift($uri);
		return $this->uri = $uri;
	}
	
	// you can render from a different LF folder. Just chdir to it before render, and it will not know you moved somewhere. this works in the index.php as well
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
	
	// Load skin data
	public function execTemplate()
	{
		// can we use home.php for this request?
		// it lets us have a unique home page.
		$file = $this->elements['home']
			? 'home'
			: 'index';
		
		ob_start();
		
		//pre($this->content);
		if(is_file($this->getTemplatePath()."$file.php")) // allow php
			include($this->getTemplatePath()."$file.php");
		else if(is_file($this->getTemplatePath()."$file.html"))
			readfile($this->getTemplatePath()."$file.html");
		else
			echo 'Template file "'.$this->getTemplatePath().$file.'.php" missing. Log into admin and select a different template with the Skins tool.';
			
		return ob_get_clean();
	}
	
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
	
	public function renderHead()
	{
		return implode("\n", [
				$this->renderJs(),
				$this->renderCss(),
				implode( "\n", $this->elements['head'] )
			]);
	}
	
	public function renderJs()
	{
		$parts = [];
		foreach($this->elements['js'] as $js)
		{
			$parts[] = '<script src="'.$js.'" type="text/javascript"></script>';
		}
		return implode("\n", $parts);
	}
	
	public function renderCss()
	{
		$parts = [];
		foreach($this->elements['css'] as $css)
			$parts[] = '<link rel="stylesheet" href="'.$css.'" />';
		return implode("\n", $parts);
	}
	
	public function setHome($bool)
	{
		$this->elements['home'] = $bool;
		return $this->save();
	}
	
	public function getTemplatePath()
	{
		$skinDir = isAdmin() 
			? 'system/admin/skin/' 
			: 'skins/';
			
		return LF.$skinDir.$this->getTemplateName().'/';
	}
	
	public function getTemplateName()
	{
		return $this->elements['skin'];
	}
	
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