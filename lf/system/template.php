<?php

#Do not change methods in here at all
/**
*@author kyle S <kyle@kylesorrels.com>
*@license whatever littlefootcms is
*@package littlefootcms
*
*/
class Template
{
	//should have contants to define application root
	protected $docRoot = APPLICATION_ENV;
	public function __construct($vars =null,$templateName = null){
		//set templatename
		$this->templateName=(is_null($templateName))?'':$templateName;
		//first off we need the url
		$uri = $_SERVER['REDIRECT_URL'];
		$this->uriPath = $uri;
		$uri = explode('/',$uri);
		array_shift($uri);
		$this->uri = $uri;

		//get the view variables
		if(!is_null($vars)){
			foreach($vars as $key => $var){
				$this->$key = $var;
			}
		}
	}
	public function display($viewPath){
		$this->renderHeader();
		$this->includeScripts();
		$this->renderNavbar();
		$this->renderBreadcrumb();
		$this->showMess();
		include($viewPath);
		$this->renderFooter();
		unset($_POST);
		return;
	}
	public function ajaxRender($viewPath){
		include($viewPath);
		return;
	}
	public function render404(){
		include($this->docRoot.'/errors/404.html');
	}
	public function renderHeader(){
		include($this->docRoot.'/html/template/topbar.php');
	}
	public function renderNavbar(){
		include($this->docRoot.'/html/template/navigation.php');
	}
	public function renderFooter(){
		include($this->docRoot.'/html/template/footer.php');
	}
	public function showMess(){
		if( isset($_SESSION['messages'][0] )&& is_array($_SESSION['messages'][0]) ){
			foreach($_SESSION['messages'] as $mess){
				echo Message::create($mess);
			}
		$_SESSION['messages']=array();
		}
	}
	public function includeScripts(){
		include($this->docRoot.'/html/template/scripts.php');
	}
	public function includeAdminScripts(){
		include($this->docRoot.'/html/admin/scripts.php');

	}
	public function renderBreadcrumb(){
		if(!isset($this->pageName) || $this->pageName ==''){
			$this->pageName = 'Welcome';
		}
		if(!isset($this->subtitle) || $this->subtitle ==''){
			$this->subtitle = 'Door application';
		}

		include($this->docRoot.'/html/template/breadcrumb.php');
	}
	public function getNavBar(){
		include($this->docRoot.'/config/navigation.php');
		return $navigation;
	}
	public static function getSideBarWidget(){
		return 'sidebar';
	}
	public function displayLogin($errorMess) {
		$this->errorMess = $errorMess;
		include($this->docRoot.'/html/login.php');
		exit;
	}
}