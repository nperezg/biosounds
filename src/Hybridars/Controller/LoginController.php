<?php

namespace Hybridars\Controller;

use Hybridars\Utils\Auth;

class LoginController
{
    protected $template = 'login.phtml';
    protected $view;

    public function __construct() {
		$this->view = new View();  
    }
    
    public function getContent(){
	}
	
    public function create() {
		$this->getContent();
        return $this->view->render($this->template);
    }
    
    public function log(){
		$userName = strtolower(filter_var($_POST["inputUsername"], FILTER_SANITIZE_STRING));
		$password = filter_var($_POST["inputPassword"], FILTER_SANITIZE_STRING);
		if(Auth::login($userName, $password)){
			header('Location: '.APP_URL);
			exit();
		}
		else 
			throw new \Exception("Invalid Username or Password, try again.");
	}   
	
	public function logout(){
		Auth::logout();
		header('Location: '.APP_URL);
		exit();
	}   
}
