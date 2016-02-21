<?php

namespace lf;

class Template
{
	//should have constants to define application root
	protected $docRoot = LF;
	private $ajaxMode = false;
	
	public function __construct($lf = NULL){
		
		$this->lf = $lf;
		
		// ajax mode (should maybe go into LF class)
		if( (!empty($_SERVER['HTTP_X_REQUESTED_WITH']))
		  && ( strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
		  || (strpos($this->lf->action,'ajax')=== 0)
		  ){
	            $this->ajaxMode = true;
	    }
		
		//set templatename
		$this->skinName=(is_null($lf))?'':$lf->select['template'];
	}
	
	public function getApps($id = NULL)
	{
		$this->lf->hook_run('pre template getApps');
		
		// if full cms string provided, return all linked apps
		if($this->lf->simplecms == '_lfcms')
			$this->links = orm::q('lf_links')->filterByinclude($id)->get();
		
		// otherwise, assign only that app
		else
			$this->links[0] = array(
				'id' => 0, 
				'app' => $this->lf->simple_cms,
				'ini' => '',
				'section' => 'content'
			);
		
		$this->lf->hook_run('post template getApps');
		
		return $this;
	}
	
	public function getContent()
	{
		$this->lf->hook_run('pre template getcontent');
		
		
		
		$this->lf->hook_run('post template getcontent');
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