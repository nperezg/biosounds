<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Provider\CollectionProvider;
use Hybridars\BioSounds\Utils\Auth;

class TopController
{
    protected $template = 'top.phtml';
    protected $view;

    public function __construct()
    {
		$this->view = new View(); 
		$this->view->extraMenu = "";
		$this->view->topRightMenu = "";		 
    }

    /**
     * @throws \Exception
     */
    private function getContent()
    {
		if (Auth::isUserLogged()) {
			$this->addCollectionsMenu();
			$this->view->extraMenu .= "<li><a target=\"_blank\" href=\"https://docs.google.com/document/d/1mdZPvGXGbbrxbX7Ms2li9B-fxYCKppjJgpT1RID4hJY/edit?pli=1\">Guide</a></li>";
		}
		$this->addRightMenu();	
	}

    /**
     * @throws \Exception
     */
	private function addCollectionsMenu()
    {
		$this->view->extraMenu .= "<li class='dropdown'><a aria-expanded='true' tabindex='0' ";
        $this->view->extraMenu .= "data-toggle='dropdown' data-submenu='' href='#'>Collections<span class='caret'>";
        $this->view->extraMenu .= "</span></a><ul class='dropdown-menu'>";

        $list = (new CollectionProvider())->getList();
		if (!empty($list)) {
			foreach($list as $item) {
				$this->view->extraMenu .= "<li><a href='" . APP_URL . '/collection/show/' . $item->getId() . '/1';
                $this->view->extraMenu .= "'>" . $item->getName() . '</a></li>';
			}
		}
		$this->view->extraMenu .= '</ul></li>';
	}
	
	private function addRightMenu()
    {
		if (Auth::isUserLogged()) {
			$username = Auth::getUserName();

			//include("include/check_system.php");
			
			$this->view->topRightMenu .= "<li class='dropdown'>";
			$this->view->topRightMenu .= "<a class='user' aria-expanded='true' tabindex='0' data-toggle='dropdown' data-submenu='' href='#'><span class='glyphicon glyphicon-user'></span> $username</a>";
			$this->view->topRightMenu .= "<ul class='dropdown-menu'><li hidden><a href='user/edit'>Settings</a></li>";

			if (Auth::isUserAdmin()) {
				$this->view->topRightMenu .= "<li><a href='" . APP_URL. "/admin'>Administration</a></li>";
			}
			$this->view->topRightMenu .= "<li role='separator' class='divider'></li><li><a href='" . APP_URL. "/login/logout'>Logout</a></li></ul>";
						
		}
		else {
			$this->view->topRightMenu .= " <li><a class='log' data-toggle='collapse' href='#collapseExample' aria-expanded='false' aria-controls='collapseExample'><span class='glyphicon glyphicon-log-in'></span> Login</a></li>";
		}
	}

    /**
     * @return string
     * @throws \Exception
     */
    public function create()
    {
		$this->getContent();
        return $this->view->render($this->template);
    }
}
