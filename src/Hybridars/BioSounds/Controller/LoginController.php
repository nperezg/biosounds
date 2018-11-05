<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Classes\BaseController;
use Hybridars\BioSounds\Exception\AuthenticationException;
use Hybridars\BioSounds\Utils\Auth;

class LoginController extends BaseController
{
    public function create()
    {
    }

    /**
     * @throws \Exception
     */
    public function log(){
		$userName = strtolower(filter_var($_POST["inputUsername"], FILTER_SANITIZE_STRING));
		$password = filter_var($_POST["inputPassword"], FILTER_SANITIZE_STRING);
		if(Auth::login($userName, $password)){
			header('Location: '.APP_URL);
			exit();
		}
		else 
			throw new AuthenticationException();
	}

	public function logout(){
		Auth::logout();
		header('Location: '.APP_URL);
		exit();
	}   
}
