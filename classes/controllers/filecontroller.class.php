<?php

namespace classes\controllers;

use \classes\controllers\View;
use \classes\controllers\UserController;
use \classes\controllers\CollectionController;
use \classes\utils\Auth;
use \classes\utils\Utils;
use \classes\models\File;

class FileController {

   // protected $template = "fileupload.phtml";
    protected $view;

    public function __construct() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view = new View();
    }
    
    public function create(){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		return $this->view->render($this->template);
	}
	
	public function checkUploadProcess(){
		$uploadRunning = Utils::isUploadRunning();
		return $uploadRunning;
	}
    
    /*public function upload($colID) {		
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		if(Utils::isUploadRunning())
			throw new \Exception(ERROR_UPLOAD_RUNNING); 
		
		$randomNumber = mt_rand();
		mkdir("tmp/$randomNumber", 0777);
		setcookie("random_upload_dir", $randomNumber, time()+(3600*24*30), APP_DIR);

		//$this->view->soundExtensions = Utils::getSoundExtensions();
		$this->view->colID = $colID;
		
        return $this->view->render($this->template);
    }    */
    
    public function save(){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		} if(Utils::isUploadRunning())
			throw new \Exception(ERROR_UPLOAD_RUNNING); 
			
		$uploadDir = "tmp/".$_COOKIE["random_upload_dir"]."/";

		#Fields
		$colID = filter_var($_POST["colID"], FILTER_SANITIZE_NUMBER_INT);
		$siteID = filter_var($_POST["site"], FILTER_SANITIZE_NUMBER_INT);
		$sensorID = filter_var($_POST["sensor"], FILTER_SANITIZE_NUMBER_INT);
		$dateFromFile = isset($_POST["dateFromFile"]) ? filter_var($_POST["dateFromFile"], FILTER_VALIDATE_BOOLEAN) : false;
		$timecoded = filter_var($_POST["time"], FILTER_SANITIZE_NUMBER_INT);
		$datecoded = filter_var($_POST["date"], FILTER_SANITIZE_NUMBER_INT);
		
		if (is_dir($uploadDir) && opendir($uploadDir)) {
			$handle = opendir($uploadDir);
			$files = array();
			while ($file = readdir($handle)) {
				if ($file != "." && $file != "..") {
					array_push($files, $file);
				}
			}
			closedir($handle);
		}

		$userID = Auth::getUserID();
		
		$file = new File();
		$fileToAddID = $file->insertFilePath($userID, ABSOLUTE_DIR.$uploadDir);
		#WA format: YYYMMDD_HHMMSS

		$fileExtensions = Utils::getSoundExtensions();

		foreach ($files as $fileName) {			
			if($dateFromFile) {
				$fileExtension= explode("." , $fileName);
				$extOffset = strlen($fileExtension[1]);
				
				$yearcoded = substr($fileName, -16 - $extOffset, 4);
				$monthcoded = substr($fileName, -12 - $extOffset, 2);
				$daycoded = substr($fileName, -10 - $extOffset, 2);
				$hourcoded = substr($fileName, -7 - $extOffset, 2);
				$minutescoded = substr($fileName, -5 - $extOffset, 2);
				$secondscoded = substr($fileName, -3 - $extOffset, 2);
					
				$datecoded = $yearcoded . "-" . $monthcoded . "-" . $daycoded;
				$timecoded = $hourcoded . ":" . $minutescoded . ":" . $secondscoded;
			} 				

			$dirID = rand(1,100);

			$filePath = ABSOLUTE_DIR.$uploadDir . $fileName;

			#Check that the extension is a valid sound file
			#Quick check to avoid adding non-sound files
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);
			
			if (substr_count($fileExtensions, strtolower($extension)) > 0){
				$values = array();
				$values[":fileID"] = $fileToAddID;
				$values[":filePath"] = $filePath;
				$values[":fileName"] = $fileName;
				$values[":date"] = $datecoded;
				$values[":time"] = $timecoded;
				$values[":siteID"] = $siteID;
				$values[":colID"] = $colID;
				$values[":dirID"] = $dirID;
				$values[":sensorID"] = $sensorID;
				$file->insertFileToAdd($values);
			}
		}

		//if ($special_noprocess == FALSE){
			Utils::processFiles(ABSOLUTE_DIR, $uploadDir);
//		}
	}	
}

?>
