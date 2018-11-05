<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Recording;
use Hybridars\BioSounds\Exception\File\FileNotFoundException;
use Hybridars\BioSounds\Exception\File\FileQueueNotFoundException;
use Hybridars\BioSounds\Provider\FileProvider;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Service\FileService;
use Hybridars\BioSounds\Service\SpectrogramService;
use Hybridars\BioSounds\Utils\Auth;

/**
 * Class FileController
 * @package Hybridars\BioSounds\Controller
 */
class FixController
{
    /**
     * FileController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}

        define('ABSOLUTE_DIR', "/var/www/biosounds/");
        define('TMP_DIR', "/var/www/biosounds/tmp");
    }

    /**
      * @return array
     * @throws \Exception
     */
    public function fix(int $soundId)
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		} 
		
		try {
            if (empty($sound = (new RecordingProvider())->getSimpleSound($soundId))) {
                throw new FileNotFoundException($soundId);
            }
            (new SpectrogramService())->generateImages($sound);
        } catch (\PDOException $exception) {
		    error_log($exception);
		    throw new \Exception('There was an error when inserting in the database.');
        } catch (\Exception $exception) {
		    error_log($exception);
		    throw $exception;
        }
    }
}
