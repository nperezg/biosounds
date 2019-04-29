<?php

namespace Hybridars\BioSounds\Security;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\Setting;

class Session
{
    const CONFIG_FILENAME = 'config/config.ini';

    /**
     * @var array
     */
    private $config;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        $this->config = parse_ini_file(self::CONFIG_FILENAME);

        Database::$connection = new \PDO(
            $this->config['DRIVER'].':host='.$this->config['HOST'].';dbname='.$this->config['DATABASE'],
            $this->config['USER'],
            $this->config['PASSWORD'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]

        );
    }

    /**
     * @throws \Exception
     */
    public function startSecureSession()
    {
        $session_name = 'biosounds_session';
        $secure = false;
        $httpOnly = true;
        // Forces sessions to only use cookies.
        if (!ini_set('session.use_only_cookies', 1)) {
            error_log('Session: error when setting cookies.');
            throw new \Exception('There was a problem with your session. Please contact the administrator.');
        }

        // Gets current cookies params.
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params(1800, $cookieParams['path'], $cookieParams['domain'], $secure, $httpOnly);

        //No cache for avoiding back button 'document expired' problem
        header("Cache-Control: no cache");
        session_cache_limiter("private, must-revalidate");

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
    }

    /**
     * @throws \Exception
     */
    public function initGlobals()
    {
        define('APP_URL', $this->config['APP_URL']);
        define('IMAGES_URL', APP_URL . $this->config['IMAGES_URL']);
        define('PROJECT_IMAGES_URL', IMAGES_URL . $this->config['PROJECT_IMAGES_URL']);
        define('ABSOLUTE_DIR', $this->config['ABSOLUTE_DIR']);
        define('TEMPLATES_DIR', $this->config['TEMPLATES_DIR']);
        define('CACHE_DIR', $this->config['CACHE_DIR']);
        define('LOGO', IMAGES_URL . $this->config['LOGO']);
        define('TMP_DIR', $this->config['TMP_DIR']);

        define('WINDOW_WIDTH', $this->config['WINDOW_WIDTH']);
        define('SPECTROGRAM_LEFT', $this->config['SPECTROGRAM_LEFT']);
        define('SPECTROGRAM_RIGHT', $this->config['SPECTROGRAM_RIGHT']);
        define('SPECTROGRAM_HEIGHT', $this->config['SPECTROGRAM_HEIGHT']);

        define('ERROR_NOT_LOGGED', $this->config['ERROR_NOT_LOGGED']);
        define('ERROR_EMPTY_ID', $this->config['ERROR_EMPTY_ID']);
        define('ERROR_NOT_ALLOWED', $this->config['ERROR_NOT_ALLOWED']);
        define('ERROR_NO_ADMIN', $this->config['ERROR_NO_ADMIN']);
        define('ERROR_UPLOAD_RUNNING', $this->config['ERROR_UPLOAD_RUNNING']);

        if (!isset($_SESSION['settings'])) {
            $_SESSION['settings'] = (new Setting())->getList();
        }
    }
}
