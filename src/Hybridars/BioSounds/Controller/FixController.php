<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Exception\File\FileNotFoundException;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Service\ImageService;
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
     * @param int $recordingId
     * @throws \Exception
     */
    public function fix(int $recordingId)
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		} 
		
		try {
            if (empty($recording = (new RecordingProvider())->getBasic($recordingId))) {
                throw new FileNotFoundException($recordingId);
            }
            (new ImageService())->generateImages($recording);
        } catch (\PDOException $exception) {
		    error_log($exception);
		    throw new \Exception('There was an error when inserting in the database.');
        } catch (\Exception $exception) {
		    error_log($exception);
		    throw $exception;
        }
    }
}
