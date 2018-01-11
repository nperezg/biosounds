<?php

namespace Hybridars\Controller;

use Hybridars\Entity\SoundTag;
use Hybridars\Entity\Permission;
use Hybridars\Entity\User;
use Hybridars\Utils\Auth;

class SoundTagController
{
    protected $template = 'soundTag.phtml';
    protected $callDist = "soundDistEstimation.phtml";
    protected $view;

    private $soundID;
    
    public function __construct() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->initView();
    }
    
    public function create() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->getContent();
        return $this->view->render($this->template);
    }
    
    public function showCallDistance($tagID){
		$this->view->tagIDLabel = SoundTag::TAG_ID;
		$this->view->callDistLabel = SoundTag::CALL_DISTANCE;
		$this->view->tagID = $tagID;
	    return $this->view->render($this->callDist);
	}
    
	public function addNew(){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		
		if(isset($_POST["t_min"]) && isset($_POST["t_max"]) && isset($_POST["f_min"]) && isset($_POST["f_max"])){
			$this->view->timeMin = filter_var($_POST["t_min"], FILTER_SANITIZE_STRING);
			$this->view->timeMax = filter_var($_POST["t_max"], FILTER_SANITIZE_STRING);
			$this->view->freqMin = filter_var($_POST["f_min"], FILTER_SANITIZE_STRING);
			$this->view->freqMax = filter_var($_POST["f_max"], FILTER_SANITIZE_STRING);			
		} else
			throw new \Exception("Data not set.");
			
		$this->view->userFullName = Auth::getUserName();
		
		$this->view->submitFormFunction = "submitTagForm()";
		$this->view->distance_estimated = false;
			
		$this->setTypeSelect();
			
		return $this->create();
	}
	
	public function edit($tagID){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($tagID)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		if(!Auth::isUserAdmin() && (!isset($_SESSION["user_col_permission"]) || empty($_SESSION["user_col_permission"]))){
			throw new \Exception(ERROR_NOT_ALLOWED);
		}
		
		$soundTag = new SoundTag();
		$tagData = $soundTag->getSoundTag($tagID);
		
		// USERS CONTROL
		$hasReviewPerm = false;
		$this->view->displayDeleteBtn = "";
		$this->view->submitFormFunction = "submitTagForm()";
			
		if(Auth::isUserAdmin() || $tagData[SoundTag::USER_ID] != Auth::getUserLoggedID()){
			$userColPerm = $_SESSION["user_col_permission"];		
			$permission = new Permission();	
			$hasReviewPerm = Auth::isUserAdmin() ? true : $permission->isReviewPermission($userColPerm);	
			$hasViewPerm = Auth::isUserAdmin() ? true : $permission->isViewPermission($userColPerm);
			if(!$hasReviewPerm && !$hasViewPerm)
				throw new \Exception(ERROR_NOT_ALLOWED);
				
			$this->view->disableForm = !Auth::isUserAdmin() ? "true" : "";
			$this->view->displaySaveBtn = "hidden";	
			if (Auth::isUserAdmin() || $hasReviewPerm) {
				$this->view->submitFormFunction = Auth::isUserAdmin() ? "submitAllForms()" : "submitReviewForm()";
				$this->view->displaySaveBtn = "";
			}
		}
		//
					
		$this->view->tagID = $tagData[SoundTag::TAG_ID];
		$this->view->callDistance = $tagData[SoundTag::CALL_DISTANCE];
		$this->view->distance_not_estimable = $tagData[SoundTag::DISTANCE_NOT_ESTIMABLE] ? "checked" : "";   
		$this->view->timeMin = round($tagData["time_min"], 1);
		$this->view->timeMax = round($tagData["time_max"], 1);
		$this->view->freqMin = $tagData["freq_min"];
		$this->view->freqMax = $tagData["freq_max"];
		$this->view->animalName = $tagData["AnimalName"];
		$this->view->animalID = $tagData[SoundTag::ANIMAL_ID];
		$this->view->uncertain = ($tagData[SoundTag::UNCERTAIN] ? "checked" : "");
		$this->view->reference_call = ($tagData[SoundTag::REFERENCE_CALL] ? "checked" : "");
		$this->view->numberIndiv = $tagData[SoundTag::NUMBER_INDIVIDUALS];
		$this->view->comments = $tagData[SoundTag::COMMENTS];
		$this->view->userFullName = $tagData[User::FULL_NAME];
		$this->view->reference_call = ($tagData["reference_call"] ? "checked" : "");
		
		$this->setTypeSelect($tagData["type"]);
		
		if($hasReviewPerm)
			$this->view->reviewPanel = $this->setReviewPanel($tagID);
		
		return $this->create();
	}
	
	public function save(){
	  if(!Auth::isUserLogged()){
	    throw new \Exception(ERROR_NOT_LOGGED);
	  }

	  $data[SoundTag::UNCERTAIN] = 0;
	  $data[SoundTag::REFERENCE_CALL] = 0;
	  $data[SoundTag::DISTANCE_NOT_ESTIMABLE] = 0;
	  
	  foreach($_POST as $key => $value){
	    if ($value == "on")
	      $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
	    else
	      $data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
	      
	    if($value == null && $key == SoundTag::CALL_DISTANCE) {
	      $data[$key] = NULL;
	    }
	  }
	  
	  $soundTag = new SoundTag();
	  
	  if(isset($_POST[SoundTag::TAG_ID]) && !empty($_POST[SoundTag::TAG_ID])){	
		  return $soundTag->updateSoundTag($data);
	  }
	  else {	
		  $data[SoundTag::USER_ID] = Auth::getUserLoggedID();
		  if($data[SoundTag::DISTANCE_NOT_ESTIMABLE] != 1)
		    $data[SoundTag::DISTANCE_NOT_ESTIMABLE] = NULL;
		  unset($data[SoundTag::TAG_ID]);
		  return $soundTag->insertSoundTag($data);
	  }
	}
	
	public function delete($tagID){
		$soundTag = new SoundTag();
		$result = $soundTag->deleteSoundTag($tagID);
		return $result;
	}
	
	private function setReviewPanel($tagID){
		$tagReview = new TagReviewController();
		return $tagReview->show($tagID);	
	}
	
	private function setTypeSelect($type = NULL){
		$this->view->type .= "<option value='call' " . ($type =='call' ? "selected" : "") . ">Call</option>";
		$this->view->type .= "<option value='song' " . ($type =='song' ? "selected" : "") . ">Song</option>";
		$this->view->type .= "<option value='non-vocal' " . ($type =='non-vocal' ? "selected" : "") . ">Non-vocal</option>";
		$this->view->type .= "<option value='searching (bat)' " . ($type =='searching (bat)' ? "selected" : "") . ">Searching (bat)</option>";
		$this->view->type .= "<option value='feeding (bat)' " . ($type =='feeding (bat)' ? "selected" : "") . ">Feeding (bat)</option>";
		$this->view->type .= "<option value='social (bat)' " . ($type =='social (bat)' ? "selected" : "") . ">Social (bat)</option>";
	}
	
	private function initView(){
		$this->view = new View();  
		$this->view->tagID = "";
		$this->view->callDistance = "";
		$this->view->timeMin = "";
		$this->view->timeMax = "";
		$this->view->freqMin = "";
		$this->view->freqMax = "";
		$this->view->animalName = "";
		$this->view->animalID = "";
		$this->view->uncertain = "";
		$this->view->numberIndiv = "";
		$this->view->comments = "";
		$this->view->displayDeleteBtn = "hidden";
		$this->view->displaySaveBtn = "";
		$this->view->soundName = "";
		$this->view->disableForm = "false";	
		$this->view->reviewPanel = "";	
		$this->view->userFullName = "";	
	}
	
	private function getContent(){
	    $this->view->soundName = isset($_POST['sound_name']) ? $_POST['sound_name'] : NULL;
	    $soundID = isset($_POST['sound_id']) ? $_POST['sound_id'] : NULL;
		$this->view->soundID = $soundID;
		$this->view->specWidth = filter_var($_POST["specWidth"], FILTER_SANITIZE_STRING);
		$this->view->specHeight = filter_var($_POST["specHeight"], FILTER_SANITIZE_STRING);
		$this->view->timeMinView = filter_var($_POST["timeMinView"], FILTER_SANITIZE_STRING);
		$this->view->timeMaxView = filter_var($_POST["timeMaxView"], FILTER_SANITIZE_STRING);
		$this->view->freqMinView = filter_var($_POST["freqMinView"], FILTER_SANITIZE_STRING);
		$this->view->freqMaxView = filter_var($_POST["freqMaxView"], FILTER_SANITIZE_STRING);
		$this->view->animalIDLabel = SoundTag::ANIMAL_ID;	
		$this->view->soundIDLabel = SoundTag::SOUND_ID;	
		$this->view->tagIDLabel = SoundTag::TAG_ID;	
		$this->view->numberIndivLabel = SoundTag::NUMBER_INDIVIDUALS;	
	}
}
