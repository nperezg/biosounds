<?php

	include_once("config.inc");

	use Hybridars\Database\Database;
	use Hybridars\Entity\Settings;

	Database::$connection = new \PDO(DRIVER.':host='.HOST.';dbname='.DATABASE, USER, PASSWORD);
	Database::$connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

	function secure_session_start() {
		$session_name = 'secure_session_id'; 
		$secure = false;
		$httponly = true;
		// Forces sessions to only use cookies.
		if (!ini_set('session.use_only_cookies', 1)) {
			throw new \Exception("Error on setting cookies.");
		}
		// Gets current cookies params.
		$cookieParams = session_get_cookie_params();
		session_set_cookie_params(1800, $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
		// Sets the session name to the one set above.
		session_name($session_name);
		session_start();            
		
		if (!isset($_SESSION['regenerate_timeout'])) {
			session_regenerate_id(true);
			$_SESSION['regenerate_timeout'] = time();
		}
		// Regenerate session ID every five minutes:
		if ($_SESSION['regenerate_timeout'] < time() - 300) {
			session_regenerate_id(true);
			$_SESSION['regenerate_timeout'] = time();
		}

		if(!isset($_SESSION["settings"])){
			$settings = new Settings();
			$_SESSION["settings"] = $settings->getSettings();			
		}
	}
