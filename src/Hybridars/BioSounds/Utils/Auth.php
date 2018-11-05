<?php

namespace Hybridars\BioSounds\Utils;

use Hybridars\BioSounds\Entity\User;

class Auth
{
	static function getUserLoggedID(){
		return $_SESSION['user_id'];
	}	
	
	static function getUserName(){
		if (isset($_SESSION["username"]))
		    return $_SESSION["username"];
		return "";
	}
	
	static function login($username, $password){
		$user = new User();
		$encPwd = $user->getPassword($username);
		
		if (Utils::checkPasswords($password, $encPwd)) {
			$user_browser = $_SERVER['HTTP_USER_AGENT'];
			$user_id = $user->getUserID($username);
			$user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection 
			$_SESSION['user_id'] = $user_id;

			$username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection 
			$_SESSION['username'] = $username;
			$_SESSION['login_string'] = hash('sha512', $encPwd . $user_browser);
			return true;
		} else {
			/*$now = time();
			$mysqli->query("INSERT INTO login_attempts(user_id, time)
							VALUES ('$user_id', '$now')");*/
			return false;
		}
    }
	
	static function logout(){
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		session_unset();
		session_destroy();
	}
	
	static function isUserLogged(){
		if (isset($_SESSION["login_string"])){
			$userID = $_SESSION["user_id"];
			if($userID == NULL || empty($userID))
				return false;

			$user = new User();
			#get host name of user
		/*	$remote_host = $_SERVER['REMOTE_ADDR'];
			$user_loggedin = query_one("SELECT COUNT(*) FROM Cookies WHERE user_id = '$cookie_to_test1' AND cookie = '$cookie_to_test2' AND hostname = '$remote_host' LIMIT 1", $connection);*/

		    return $user->isUserActive($userID);
		}
		else
			return false;
	}
	
	static function isUserAdmin(){
		$user = new User();
		if(!self::isUserLogged())
			return false;
		
		$userID = $_SESSION["user_id"];	
		return $user->isUserAdmin($userID);
	}
	
	static function getUserID(){
		if(isset($_SESSION["user_id"]))
			return $_SESSION["user_id"];
		else
			return null;
	}
}
