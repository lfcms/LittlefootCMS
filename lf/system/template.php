<?php

/**
*@author kyle S <kyle@kylesorrels.com>
*@license whatever littlefootcms is
*@package littlefootcms
*
*/
class Template
{
	//should have contants to define application root
	protected $docRoot = DOCUMENT_ROOT;
	private $ajaxMode = false;
	public function __construct($vars =null,$templateName = null){
		//set templatename
		$this->templateName=(is_null($templateName))?'':$templateName;

		//first off we need the url


		//get the view variables
		if(!is_null($vars)){
			if(is_array($vars)){
				foreach($vars as $key => $var){
					$this->$key = $var;
				}
			}else{
				$this->vars = $vars;
			}
		}
		// ajax mode
		if( (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
		  && ( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
		  || (strpos($this->action,'ajax')=== 0)
		  ){
	            $this->ajaxMode = true;
	    }

	}
	public function getUriAsArray(){
		$uri = $_SERVER['REDIRECT_URL'];
		//set the uriPath as string.
		$this->uriPath = $uri;
		$uri = explode('/',$uri);
		//take the first / out
		array_shift($uri);
		return $this->uri = $uri;
	}
	public function display($viewPath){
		if($this->ajaxMode){
			return $this->ajaxRender();
		}
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
	public static function render404(){
		include(ROOT.'system/view/errors/404.html');
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