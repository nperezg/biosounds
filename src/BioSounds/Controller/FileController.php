<?php

namespace BioSounds\Controller;

use BioSounds\Service\FileService;
use BioSounds\Utils\Auth;

/**
 * Class FileController
 * @package BioSounds\Controller
 */
class FileController
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
    }

    /**
     * @param string $uploadDirectory
     * @return array
     * @throws \Exception
     */
    public function upload(string $uploadDirectory)
    {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		} 
		
		try {
            return (new FileService())->upload($_POST, ABSOLUTE_DIR .'tmp/' . $uploadDirectory .'/');
        } catch (\PDOException $exception) {
		    error_log($exception);
		    throw new \Exception('There was an error when inserting in the database.');
        } catch (\Exception $exception) {
		    error_log($exception);
		    throw $exception;
        }
    }
}
