<?php

namespace classes\controllers;

use \classes\controllers\View;
use \classes\models\Sound;
use \classes\models\Collection;
use \classes\models\Sensor;
use \classes\models\Site;
use \classes\utils\Auth;
use \classes\utils\Utils;

class SoundManagerController {

    protected $template = 'soundmanager.phtml';
    protected $view;
    protected $sound;
    
    private $page = 1;
    private $colID;
    private $numSounds;
    const ITEMS_PAGE = 15;

    public function __construct() {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		$this->view = new View();
		$this->view->paginator = ""; 
		$this->sound = new Sound();
    }
    
    public function create($id = NULL, $page = 1) {
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		if(isset($_POST["colID"]))
			$this->colID = filter_var($_POST["colID"], FILTER_SANITIZE_STRING); 
		
		if($id != NULL)
			$this->colID = $id;
			
		$this->page = $page;	
			
		$this->getContent();
        return $this->view->render($this->template);
    }
    
    public function save(){
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		$data = array();
		foreach($_POST as $key => $value){
			if(strpos($key, "_")){
				$type = substr($key, strpos($key, "_") + 1, strlen($key));
				$key = substr($key, 0, strpos($key, "_"));
				switch($type){
					case "date":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_STRING); 
						break;
					case "time":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_STRING); 
						break;	
					case "text":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_STRING); 
						break;
					case "hidden":
						$data[$key] =  filter_var($value, FILTER_SANITIZE_NUMBER_INT); 
						break;
				}				
			} else
				$data[$key] =  filter_var($value, FILTER_SANITIZE_STRING); 
		}	
		if(isset($data["itemID"]))
			return $this->sound->updateSound($data);
		else {
			if($this->sound->insertSound($data) > 0)
				header("Location: " . APP_URL . "/admin/sounds");
				die();
		}
	}
	
	public function delete()
	{
		if (!Auth::isUserAdmin() || !isset($_POST["id"])){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT); 
			
		$sound = $this->sound->getSound($id);		
		error_log(implode(',', $sound));				
		
		$fileName = $sound[SOUND::ORIGINAL_FILENAME];
		$colID = $sound[SOUND::COL_ID];
		$dirID = $sound[SOUND::DIR_ID];
		$audioPreviewFilename =  $sound[SOUND::AUDIO_PREVIEW];
		$absoluteDir = ABSOLUTE_DIR . 'sounds';
		$soundsDir="$absoluteDir/sounds/$colID/$dirID/";
		$imagesDir="$absoluteDir/images/$colID/$dirID/";
		$previewDir="$absoluteDir/previewsounds/$colID/$dirID/";
		
		if (unlink($soundsDir . $fileName)) {
			$data = ['itemID' => $id, SOUND::SOUND_STATUS => '9'];
			$result = $this->sound->updateSound($data);

			//Check if there are images
			$images = $this->sound->getImages($id);

			foreach ($images as $image) {
				unlink($imagesDir . $image['ImageFile']);
			}
			$result = $this->sound->deleteImages($id);			
				
			//Check if there are mp3
			if (is_file($previewDir . $audioPreviewFilename)) {
				unlink($previewDir . $audioPreviewFilename);
			}
			
			$wavFileName = substr($fileName, 0, strrpos($fileName, '.')) . '.wav';
			if (is_file($soundsDir . $wavFileName)) {
				unlink($soundsDir . $wavFileName);
			}
			
			$result = $this->sound->deleteTags($id);	
			$result = $this->sound->deleteListenLogs($id);	
		}
		return 'The sound has been successfully deleted.';
	}
    
    private function getContent(){
		if (Auth::isUserAdmin()){
			$this->view->collectionsList =  "";
			$this->view->sensorsList = ""; 
			$this->view->sitesList = ""; 
		
			// SET COLLECTIONS LIST
			$collection = new Collection();
			$cols = $collection->getBasicList();
			if($this->colID == NULL)
				$this->colID = $cols[0][Collection::PRIMARY_KEY];
				
			foreach($cols as $value){	
				$this->view->collectionsList .= "<option value='" . $value[Collection::PRIMARY_KEY] . "'" . ($this->colID == $value[Collection::PRIMARY_KEY] ? " selected>" : ">") .
				$value[Collection::NAME] . "</option>";
			}	
			//
			
			//SET SENSORS LIST	
			$sensor = new Sensor();
			$sensors = $sensor->getBasicList();	
			
			foreach($sensors as $value){	
				$this->view->sensorsList .= "<option value='" . $value[Sensor::PRIMARY_KEY] . "'>" . $value[Sensor::NAME] . "</option>";
			}			
			//
			
			//SET SITES LIST	
			$site = new Site();
			$sites = $site->getBasicList();	
			
			foreach($sites as $value){	
				$this->view->sitesList .= "<option value='" . $value[Site::PRIMARY_KEY] . "'>" . $value[Site::NAME] . "</option>";
			}			
			//
			
			$this->numSounds = $this->sound->countSoundsCollection($this->colID);		
			$this->view->numSounds = $this->numSounds;

			$this->getSoundsList($this->colID);
			$this->getPaginator();
			$this->view->colID = $this->colID;
		}
	}
	
	private function getSoundsList($colID){	
		$pageFirstItemID = 	self::ITEMS_PAGE * ($this->page - 1);
		$listSounds = $this->sound->getSoundsByCollection($colID, self::ITEMS_PAGE, $pageFirstItemID);
		
		$this->view->listSounds = "";
		
		foreach($listSounds as $soundData) {
			$disabled = "";
			$privHidden = "";
			$soundID = $soundData[Sound::ID];
			$originalFilename = $soundData[Sound::ORIGINAL_FILENAME];
			$soundName = $soundData[Sound::NAME];
			$date = $soundData[Sound::DATE];
			$time = $soundData[Sound::TIME];
			
			$this->view->listSounds .= "<tr><th scope='row'><input type='hidden' name='itemID' value='$soundID'>$soundID</th>";
			$this->view->listSounds .= "<td>$originalFilename</td>";
			$this->view->listSounds .= "<td><input type='text' name='" . Sound::NAME . "' value='$soundName'></td>";
			$this->view->listSounds .= "<td><input type='date' name='" . Sound::DATE . "' value='$date'></td>";
			$this->view->listSounds .= "<td><input type='time' name='" . Sound::TIME . "' value='$time'></td>";
			$this->view->listSounds .= "<td><a class='delete_sound' href='#' data-id= '$soundID' title='Delete Sound'><span class='glyphicon glyphicon-trash'></span></a></td>";			
			$this->view->listSounds .= "</tr>";
		}
	}
	
	private function getPaginator(){
		if($this->numSounds <= 0)
			return;

		$this->numPages = ceil($this->numSounds / self::ITEMS_PAGE);
		
		if($this->page > $this->numPages) 
			$this->page = 1;

		for ($i = 1; $i <= $this->numPages; $i++) {
			$active = $this->page == $i ? "class='active'" : "";
			$this->view->paginator .= "<li $active><a href='admin/sounds/$this->colID/$i'>$i <span class='sr-only'></span></a></li>";
		}
	}	
}

?>
