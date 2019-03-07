<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Species;
use Hybridars\BioSounds\Entity\Tag;
use Hybridars\BioSounds\Entity\Permission;
use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Utils\Auth;

class TagController
{
    protected $template = 'tag.phtml';
    protected $callTemplate = "callEstimation.phtml";
    protected $view;

    /**
     * TagController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->initView();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function create()
    {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->getContent();
        return $this->view->render($this->template);
    }

    /**
     * @param $tagId
     * @return mixed
     */
    public function showCallDistance($tagId)
    {
		$this->view->tagIdLabel = Tag::ID;
		$this->view->callDistLabel = Tag::CALL_DISTANCE;
		$this->view->tagId = $tagId;
	    return $this->view->render($this->callTemplate);
	}

    /**
     * @return mixed
     * @throws \Exception
     */
	public function addNew()
    {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		if (isset($_POST["t_min"]) && isset($_POST["t_max"]) && isset($_POST["f_min"]) && isset($_POST["f_max"])) {
			$this->view->minTime = filter_var($_POST["t_min"], FILTER_SANITIZE_STRING);
			$this->view->maxTime = filter_var($_POST["t_max"], FILTER_SANITIZE_STRING);
			$this->view->minFreq = filter_var($_POST["f_min"], FILTER_SANITIZE_STRING);
			$this->view->maxFreq = filter_var($_POST["f_max"], FILTER_SANITIZE_STRING);
		} else
			throw new \Exception("Data not set.");
			
		$this->view->userFullName = Auth::getUserName();
		
		$this->view->submitFormFunction = "submitTagForm()";
		$this->view->distance_estimated = false;
			
		$this->setTypeSelect();
			
		return $this->create();
	}

    /**
     * @param int $tagId
     * @return mixed
     * @throws \Exception
     */
	public function edit(int $tagId)
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($tagId)){
			throw new \Exception(ERROR_EMPTY_ID);
		}

		if (!Auth::isUserAdmin()
            && (!isset($_SESSION["user_col_permission"])
                || empty($_SESSION["user_col_permission"]))
        ) {
			throw new \Exception(ERROR_NOT_ALLOWED);
		}
		
		$tagData = (new Tag())->get($tagId);

		// USERS CONTROL
		$hasReviewPerm = false;
		$this->view->displayDeleteBtn = '';
		$this->view->submitFormFunction = "submitTagForm()";
			
		if (Auth::isUserAdmin() || $tagData[Tag::USER_ID] != Auth::getUserLoggedID()) {
			$userColPerm = $_SESSION["user_col_permission"];		
			$permission = new Permission();	
			$hasReviewPerm = Auth::isUserAdmin() ? true : $permission->isReviewPermission($userColPerm);	
			$hasViewPerm = Auth::isUserAdmin() ? true : $permission->isViewPermission($userColPerm);
			if (!$hasReviewPerm && !$hasViewPerm) {
                throw new \Exception(ERROR_NOT_ALLOWED);
            }
				
			$this->view->disableForm = !Auth::isUserAdmin() ? "true" : "";
			$this->view->displaySaveBtn = 'hidden';
			if (Auth::isUserAdmin() || $hasReviewPerm) {
				$this->view->submitFormFunction = Auth::isUserAdmin() ? "submitAllForms()" : "submitReviewForm()";
				$this->view->displaySaveBtn = "";
			}
		}

		if (!Auth::isUserAdmin() && $tagData[Tag::USER_ID] != Auth::getUserLoggedID()) {
            $this->view->displayDeleteBtn = 'hidden';
        }
		//
					
		$this->view->tagId = $tagData[Tag::ID];
		$this->view->callDistance = $tagData[Tag::CALL_DISTANCE];
		$this->view->distance_not_estimable = $tagData[Tag::DISTANCE_NOT_ESTIMABLE] ? "checked" : "";
		$this->view->minTime = round($tagData[Tag::MIN_TIME], 1);
		$this->view->maxTime = round($tagData[Tag::MAX_TIME], 1);
		$this->view->minFreq = $tagData[TAG::MIN_FREQ];
		$this->view->maxFreq = $tagData[TAG::MAX_FREQ];
		$this->view->binomial = $tagData[Species::BINOMIAL];
		$this->view->speciesId = $tagData[Tag::SPECIES_ID];
		$this->view->uncertain = ($tagData[Tag::UNCERTAIN] ? "checked" : "");
		$this->view->reference_call = ($tagData[Tag::REFERENCE_CALL] ? "checked" : "");
		$this->view->numberIndiv = $tagData[Tag::NUMBER_INDIVIDUALS];
		$this->view->comments = $tagData[Tag::COMMENTS];
		$this->view->userFullName = $tagData[User::FULL_NAME];
		$this->view->reference_call = ($tagData["reference_call"] ? "checked" : "");
		
		$this->setTypeSelect($tagData["type"]);

		if ($hasReviewPerm) {
            $this->view->reviewPanel = (new TagReviewController())->show($tagId);
        }
		
		return $this->create();
	}

    /**
     * @return array|bool|int
     * @throws \Exception
     */
	public function save()
    {
	  if(!Auth::isUserLogged()){
	    throw new \Exception(ERROR_NOT_LOGGED);
	  }

	  $data[Tag::UNCERTAIN] = 0;
	  $data[Tag::REFERENCE_CALL] = 0;
	  $data[Tag::DISTANCE_NOT_ESTIMABLE] = 0;

	  foreach($_POST as $key => $value){
	    if ($value == "on")
	      $data[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
	    else
	      $data[$key] = filter_var($value, FILTER_SANITIZE_STRING);
	      
	    if($value == null && $key == Tag::CALL_DISTANCE) {
	      $data[$key] = null;
	    }
	  }

	  if (isset($_POST[Tag::ID]) && !empty($_POST[Tag::ID])) {
		  return (new Tag())->update($data);
	  } else {
		  $data[Tag::USER_ID] = Auth::getUserLoggedID();
		  if ($data[Tag::DISTANCE_NOT_ESTIMABLE] != 1) {
              $data[Tag::DISTANCE_NOT_ESTIMABLE] = null;
          }

		  unset($data[Tag::ID]);
		  return (new Tag())->insert($data);
	  }
	}

    /**
     * @param int $tagId
     * @return array|int
     * @throws \Exception
     */
	public function delete(int $tagId)
    {
        $tagProvider = new Tag();
        if (!Auth::isUserAdmin() && $tagProvider->get($tagId)['user_id'] != Auth::getUserLoggedID()) {
            throw new \Exception('The user doesn\'t have permissions to delete this tag.');
        }
		return (new Tag())->delete($tagId);
	}

    /**
     * @param null $type
     */
	private function setTypeSelect($type = null)
    {
		$this->view->type .= "<option value='call' " . ($type =='call' ? "selected" : "") . ">Call</option>";
		$this->view->type .= "<option value='song' " . ($type =='song' ? "selected" : "") . ">Song</option>";
		$this->view->type .= "<option value='non-vocal' " . ($type =='non-vocal' ? "selected" : "") . ">Non-vocal</option>";
		$this->view->type .= "<option value='searching (bat)' " . ($type =='searching (bat)' ? "selected" : "") . ">Searching (bat)</option>";
		$this->view->type .= "<option value='feeding (bat)' " . ($type =='feeding (bat)' ? "selected" : "") . ">Feeding (bat)</option>";
		$this->view->type .= "<option value='social (bat)' " . ($type =='social (bat)' ? "selected" : "") . ">Social (bat)</option>";
	}
	
	private function initView()
    {
		$this->view = new View();  
		$this->view->tagId = "";
		$this->view->callDistance = "";
		$this->view->minTime = "";
		$this->view->maxTime = "";
		$this->view->minFreq = "";
		$this->view->maxFreq = "";
		$this->view->binomial = "";
		$this->view->speciesId = "";
		$this->view->uncertain = "";
		$this->view->numberIndiv = "";
		$this->view->comments = "";
		$this->view->displayDeleteBtn = "hidden";
		$this->view->displaySaveBtn = "";
		$this->view->recordingName = "";
		$this->view->disableForm = "false";	
		$this->view->reviewPanel = "";	
		$this->view->userFullName = "";	
	}

	private function getContent()
    {
	    $this->view->soundName = isset($_POST['recording_name']) ? $_POST['recording_name'] : NULL;
	    $recordingId = isset($_POST['recording_id']) ? $_POST['recording_id'] : NULL;
		$this->view->recordingId = $recordingId;
		$this->view->specWidth = filter_var($_POST["specWidth"], FILTER_SANITIZE_STRING);
		$this->view->specHeight = filter_var($_POST["specHeight"], FILTER_SANITIZE_STRING);
		$this->view->minTimeView = filter_var($_POST["minTimeView"], FILTER_SANITIZE_STRING);
		$this->view->maxTimeView = filter_var($_POST["maxTimeView"], FILTER_SANITIZE_STRING);
		$this->view->minFreqView = filter_var($_POST["minFreqView"], FILTER_SANITIZE_STRING);
		$this->view->maxFreqView = filter_var($_POST["maxFreqView"], FILTER_SANITIZE_STRING);
		$this->view->speciesIdLabel = Tag::SPECIES_ID;
		$this->view->recordingIdLabel = Tag::RECORDING_ID;
		$this->view->tagIdLabel = Tag::ID;
		$this->view->numberIndivLabel = Tag::NUMBER_INDIVIDUALS;
	}
}
