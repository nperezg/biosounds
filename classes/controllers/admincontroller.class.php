<?php

namespace classes\controllers;

use \classes\controllers\View;
use \classes\controllers\UserController;
use \classes\controllers\CollectionController;
use \classes\controllers\SoundManagerController;
use \classes\utils\Auth;

class AdminController {

    protected $template = "admin.phtml";
    protected $view;

    public function __construct() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view = new View();
		$this->view->section = NULL;
    }
    
    public function create() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		if($this->view->section == NULL){
			$this->settings();
		}
        return $this->view->render($this->template);
    }
    
	public function settings(){
		$settingsController = new SettingsController();			
		$this->view->section = $settingsController->create();
		$this->view->sectionTitle = "Settings";
	}
	
	public function collections(){
		$collectionController = new CollectionController();			
		$this->view->section = $collectionController->getList();	
		$this->view->sectionTitle = "Collections";
	}
	
	public function users(){
		$userController = new UserController();		
		$this->view->sectionTitle = "Users";	
		$this->view->section = $userController->create();		
	}
	
    public function sounds($id = NULL, $page=1){
		$soundManagerController = new SoundManagerController();		
		$this->view->sectionTitle = "Sounds";	
		$this->view->section = $soundManagerController->create($id, $page);		
	}
}

?>
