<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Collection;
use Hybridars\BioSounds\Utils\Auth;

class TopController
{
    protected $template = 'top.phtml';
    protected $view;

    public function __construct() {
		$this->view = new View(); 
		$this->view->extraMenu = "";
		$this->view->topRightMenu = "";		 
    }
    
    private function getContent(){
		if (Auth::isUserLogged()){
			$this->addCollectionsMenu();
			$this->view->extraMenu .= "<li><a target=\"_blank\" href=\"https://docs.google.com/document/d/1mdZPvGXGbbrxbX7Ms2li9B-fxYCKppjJgpT1RID4hJY/edit?pli=1\">Guide</a></li>";
		}
		$this->addRightMenu();	
	}
	
	private function addCollectionsMenu(){
		$collectionModel = new Collection();
		$collections = $collectionModel->getFullList();
		$this->view->extraMenu .= "<li class='dropdown'><a aria-expanded='true' tabindex='0' data-toggle='dropdown' data-submenu='' href='#'>Collections<span class='caret'></span></a>
		<ul class='dropdown-menu'>";
		if(!empty($collections)){
			foreach($collections as $row){
				$this->view->extraMenu .= "<li><a href='collection/show/".$collectionModel->getPrimaryKey($row)."'>".$collectionModel->getName($row)."</a></li>";
			}
		}
		$this->view->extraMenu .= "</ul></li>";
	}
	
	private function addRightMenu() {	
		if (Auth::isUserLogged()) {
			$username = Auth::getUserName();

			//include("include/check_system.php");
			
			$this->view->topRightMenu .= "<li class='dropdown'>";
			$this->view->topRightMenu .= "<a class='user' aria-expanded='true' tabindex='0' data-toggle='dropdown' data-submenu='' href='#'><span class='glyphicon glyphicon-user'></span> $username</a>";
			$this->view->topRightMenu .= "<ul class='dropdown-menu'><li hidden><a href='user/edit'>Settings</a></li>";

			if (Auth::isUserAdmin()) {
				$this->view->topRightMenu .= "<li><a href='admin'>Administration</a></li>";
			}
			$this->view->topRightMenu .= "<li role='separator' class='divider'></li><li><a href='login/logout'>Logout</a></li></ul>";
						
		}
		else {
			$this->view->topRightMenu .= " <li><a class='log' data-toggle='collapse' href='#collapseExample' aria-expanded='false' aria-controls='collapseExample'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
			$notlogged = TRUE;
		}
	}
	
    public function create() {
		$this->getContent();
        return $this->view->render($this->template);
    }
}
