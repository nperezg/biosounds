<?php

namespace BioSounds\Controller;

use BioSounds\Exception\AuthenticationException;
use BioSounds\Utils\Auth;

class LoginController extends BaseController
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function login(): bool
    {
		$userName = strtolower(filter_var($_POST["inputUsername"], FILTER_SANITIZE_STRING));
		$password = filter_var($_POST["inputPassword"], FILTER_SANITIZE_STRING);

		if (Auth::login($userName, $password)) {
            header('Location: '.APP_URL);
			return true;
		}

        throw new AuthenticationException();
	}

	public function logout(): bool
    {
		Auth::logout();
		header('Location: '.APP_URL);
		return true;
	}   
}
