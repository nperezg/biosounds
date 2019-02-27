<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Classes\BaseController;
use Hybridars\BioSounds\Utils\Auth;

class AdminController extends BaseController
{
    protected $template = "admin.phtml";
    protected $view;

    /**
     * AdminController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view = new View();
		$this->view->section = NULL;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function create()
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		if($this->view->section == NULL){
			$this->settings();
		}
        return $this->view->render($this->template);
    }

    /**
     * @throws \Exception
     */
	public function settings()
    {
		$settingsController = new SettingController();
		$this->view->section = $settingsController->create();
		$this->view->sectionTitle = "Settings";
	}

    /**
     * @throws \Exception
     */
	public function collections()
    {
		$collectionController = new CollectionController();
		$this->view->section = $collectionController->getList();	
		$this->view->sectionTitle = "Collections";
	}

    /**
     * @throws \Exception
     */
	public function users()
    {
		$userController = new UserController();		
		$this->view->sectionTitle = "Users";	
		$this->view->section = $userController->create();		
	}

    /**
     * @param int|null $id
     * @param int $page
     * @throws \Exception
     */
    public function recordings(int $id = null, int $page = 1)
    {
		$this->view->sectionTitle = 'Recordings';
		$this->view->section = (new RecordingManagerController())->show($id, $page);
	}
}
