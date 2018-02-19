<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\Sound;
use Hybridars\BioSounds\Entity\SoundTag;
use Hybridars\BioSounds\Entity\UserPermission;
use Hybridars\BioSounds\Entity\Permission;
use Hybridars\BioSounds\Entity\User;
use Hybridars\BioSounds\Utils\Auth;
use Hybridars\BioSounds\Utils\Utils;

class SoundController
{
	const DEFAULT_TAG_COLOR = "#FFFFFF";

    protected $template = 'sound.phtml';
    protected $detailsTpl = "soundFileDetail.phtml";
    protected $view;

    private $soundID;
    private $colID;
    private $showTags = true;
    private $continuousPlay = false;
    private $estimateDistID;
    private $pageTitle;
    
    public function __construct() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$this->view = new View();  
    }
    
    public function create() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($this->soundID)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
        return $this->view->render($this->template);
    }
    
    public function show($id){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($id)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		
		$this->soundID = $id;
		
		$soundModel = new Sound();
		$soundData = $soundModel->getSound($this->soundID);

	    $this->view->soundName = $soundData["SoundName"];
	    $this->pageTitle = $this->view->soundName;
	    $this->setButtons();
	    $this->setCanvas($soundData);
	    $this->view->showTags = $this->showTags;
	}
	
	public function details($id){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		} else if(empty($id)){
			throw new \Exception(ERROR_EMPTY_ID);
		}
		
		$soundModel = new Sound();
		$soundData = $soundModel->getSound($id);

		$this->view->format = $soundData[Sound::FORMAT];
		$this->view->fileSize = $soundData[Sound::FILE_SIZE];
	    $this->view->duration = $soundData[Sound::DURATION];
		$this->view->numChannels = $soundData[Sound::NUM_CHANNELS];
	    $this->view->soundName = $soundData[Sound::NAME];
	    $this->view->samplingRate = $soundData[Sound::SAMPLING_RATE];
	    return $this->view->render($this->detailsTpl);
	}
	
	public function getTitle(){
		return $this->pageTitle;
	}
	
	private function setButtons(){
		if(isset($_POST["showTags"]))
			$this->showTags = filter_var($_POST["showTags"], FILTER_VALIDATE_BOOLEAN);
	
		if($this->showTags)
			$this->view->toggleTagsIcon = "<a class='toggleTag' href='#' onclick='return false;' title='Hide Tags'><span class='glyphicon glyphicon-eye-close'></span></a>";
		else 
			$this->view->toggleTagsIcon = "<a class='toggleTag' href='#' onclick='return false;' title='Show Tags'><span class='glyphicon glyphicon-eye-open'></span></a>";
			
		if(isset($_POST["continuous_play"])){
			$this->continuousPlay = filter_var($_POST["continuous_play"], FILTER_VALIDATE_BOOLEAN);		
			$this->view->continuousPlay = $this->continuousPlay ? "checked" : "";
		}
		if(isset($_POST["estimateDistID"])){
			$this->view->estimateDistID = filter_var($_POST["estimateDistID"], FILTER_VALIDATE_INT);
		}
	}
	
	private function setCanvas($soundData){
		$this->colID = $soundData["ColID"];
		$audioPreviewFilePath = "sounds/previewsounds/" . $soundData["ColID"] . "/" . $soundData["DirID"] . "/" . $soundData["AudioPreviewFilename"];
		$originalSoundFilePath = "sounds/sounds/" . $soundData["ColID"] . "/" . $soundData["DirID"] . "/" . $soundData["OriginalFilename"];
		$imageFilePath = $soundData["ImageFile"] != null ? "sounds/images/" . $soundData["ColID"] . "/" . $soundData["DirID"] . "/" . $soundData["ImageFile"] : null;
		$duration = $soundData["Duration"];
		$channels = $soundData["Channels"];
		$samplingRate = $soundData["SamplingRate"];
		
		$currentChannel = 1;
		
		if(isset($_POST["channel"])) {
			$currentChannel = filter_var($_POST["channel"], FILTER_SANITIZE_NUMBER_INT);
		}
		
		$this->view->channel = $currentChannel;
						
	    $fftSize = Utils::getSetting("fft"); 
	    $spectrogramPalette = Utils::getSetting("spectrogram_palette"); 
	    $filter = false;

		/* Get the spectrogram selection values to generate zoom and filter */
		if(isset($_POST["t_min"]) && isset($_POST["t_max"]) && isset($_POST["f_min"]) && isset($_POST["f_max"])){
			$timeMin = filter_var($_POST["t_min"], FILTER_SANITIZE_STRING);
			$timeMax = filter_var($_POST["t_max"], FILTER_SANITIZE_STRING);
			$freqMin = filter_var($_POST["f_min"], FILTER_SANITIZE_STRING);
			$freqMax = filter_var($_POST["f_max"], FILTER_SANITIZE_STRING);
			if(isset($_POST["filter"]))
				$filter = filter_var($_POST["filter"], FILTER_VALIDATE_BOOLEAN);
		} else {
			/* Default Data */
			$freqMin = 1;		
			$freqMax = $samplingRate / 2;
			$timeMin = 0;	
			$timeMax = $duration;
		}
	    	    
	    /* Spectrogram Image Width */
		$spectrogramWidth = WINDOW_WIDTH - (SPECTROGRAM_LEFT + SPECTROGRAM_RIGHT);
		$this->view->specWidth = $spectrogramWidth;
		$this->view->specHeight = SPECTROGRAM_HEIGHT;	

		$fileName = explode(".", $soundData["OriginalFilename"]);
		$selectedFileName = $fileName[0] . '_' . $freqMin . '-' . $freqMax . '_' . $timeMin . '-' . $timeMax . '_' . $fftSize . '_' . $currentChannel;
		$originalWavFilePath = "sounds/sounds/" . $soundData["ColID"] . "/" . $soundData["DirID"] . "/" . $fileName[0] . ".wav";
			
		if(!file_exists($originalWavFilePath)){
			exec('sox ' . $originalSoundFilePath . ' ' . $originalWavFilePath, $lastline, $retval);
			if ($retval != 0){
				throw new \Exception("There was a problem with SoX while generating the wav file. Please contact the administrator.");
			}
		}			
				
		#Generate a random number and store in session
		if(!isset($_SESSION["random_id"])){
			$randomID = mt_rand();
			$_SESSION["random_id"] = $randomID;
		} 
		else
			$randomID = $_SESSION["random_id"];
			
		if(!file_exists("tmp/$randomID"))	
			mkdir("tmp/$randomID", 0777);
			
		$spectrogramImagePath = 'tmp/' . $randomID . '/' . $selectedFileName.".png";			
		$soundFileView =  'tmp/' . $randomID .'/'. $selectedFileName . '.mp3';
		$wavFilePath = 'tmp/' . $randomID .'/'. $selectedFileName . '.wav';

		/* If spectrogram doesn't exist, generate */
		if (!file_exists($soundFileView)) {
			$time_length_s = $timeMax - $timeMin;
			$time_length = round($time_length_s * $samplingRate); //Set to number of samples
			$start_time = round($timeMin * $samplingRate); //Set to number of samples
			$fileSoundName = $selectedFileName . '.wav';
			$tempPath = 'tmp/' . $randomID .'/';
			
			if ($filter) {
				exec('sox ' . $originalSoundFilePath . ' tmp/1.' . $fileSoundName . ' trim ' . $start_time . 's ' . $time_length . 's', $lastline, $retval);
				if ($retval!=0)	{
					throw new \Exception("There was a problem with SoX. Please contact the administrator.");
				}

				exec('sox tmp/1.' . $fileSoundName . ' ' . $wavFilePath . ' filter ' . $freqMin . '-' . $freqMax . ' 512', $lastline, $retval);
				if ($retval != 0){
					#filter was deprecated, replaced with "sinc"
					exec('sox tmp/1.' . $fileSoundName . ' ' . $wavFilePath . ' sinc ' . $freqMin . '-' . $freqMax, $lastline, $retval);
					if ($retval!=0){
						throw new \Exception("There was a problem with SoX. Please contact the administrator.");
					}
				}
			}
			elseif ($timeMin != 0 || $timeMax != $duration) {
				exec('sox ' . $originalSoundFilePath . ' ' . $wavFilePath . ' trim ' . $start_time . 's ' . $time_length . 's', $lastline, $retval);
				if ($retval!=0){
					throw new \Exception("There was a problem with SoX. Please contact the administrator.");
				}
			} else {
				if(file_exists($audioPreviewFilePath)){
					copy($audioPreviewFilePath, $soundFileView);
				}
				if($currentChannel == 1 & $imageFilePath != null && file_exists($imageFilePath)){
					copy($imageFilePath, $spectrogramImagePath);
				}
				
				if(file_exists($originalWavFilePath))
					copy($originalWavFilePath, $wavFilePath);
				else {	
					exec('sox ' . $originalSoundFilePath . ' ' . $wavFilePath, $lastline, $retval);
					if ($retval!=0){
						throw new \Exception("There was a problem with SoX. Please contact the administrator.");
					}
				}
			} 
			
			/* Generation MP3 File */
			if(!file_exists($soundFileView)) {
				$player_result = Utils::player_file_mp3($wavFilePath, $samplingRate, $selectedFileName . '.mp3', $tempPath);
				if ($player_result != 0){
					throw new \Exception("There was a problem with the mp3 encoder...");
				}	
			}
			
			/// GENERATE IMAGE DEFAULT
			if(!file_exists($spectrogramImagePath)){
				exec('bin/svt.py -s ' . $spectrogramImagePath . ' -w ' . $spectrogramWidth . ' -h ' . SPECTROGRAM_HEIGHT . ' -i ' . $freqMin . ' -m ' . $freqMax . 
				' -f ' . $fftSize . ' -c ' . $currentChannel . ' -p ' . $spectrogramPalette . ' ' . $wavFilePath, $lastline, $retval);
				if ($retval != 0){
					throw new \Exception("The spectrogram image could not be created. Error executing svt.");
				}
			}
		}	
				
	    $this->view->timeMin = round($timeMin, 1);
	    $this->view->timeMax = round($timeMax, 1);
	    $this->view->freqMin = $freqMin;
	    $this->view->freqMax = $freqMax;
	    $this->view->duration = $duration;
	    $this->view->fileFreqMax = $samplingRate / 2;
	    
		if ($channels > 1) {
			$this->view->channels = "<label class='" . ($currentChannel == "1" ? "active" : "") . "'><a href='#' class='channel-left' onclick='return false;'> L </a></label> ";
			$this->view->channels .= "<span class='glyphicon glyphicon-headphones'></span> ";
			$this->view->channels .= "<label class='" . ($currentChannel == "2" ? "active" : "") . "'><a href='#' class='channel-right' onclick='return false;'> R </a></label>";
		}
		else 
			$this->view->channels = "<label>Mono File</label>";


		//Frequencies scale
		$range = $freqMax - $freqMin;
		$steps = round($range / 4);
		$freqDigits = strlen((string)$steps);
		$freqMid1 = round($freqMin + $steps, -$freqDigits + 1);
		$freqMid2 = round($freqMin + ($steps * 2), -$freqDigits + 1);
		
		# row heights calculation
		$freqMaxHeight = SPECTROGRAM_HEIGHT * ((($freqMax - $freqMid2) / $range) / 2);
		$freqMid2Height = $freqMaxHeight / 2 + SPECTROGRAM_HEIGHT * ((($freqMid2 - $freqMid1) / $range) / 2);
		$freqMid1Height = $freqMid2Height / 2 + SPECTROGRAM_HEIGHT * ((($freqMid1 - $freqMin) / $range) / 2);
		$freqMinHeight =  $freqMid1Height / 2 + SPECTROGRAM_HEIGHT * ((($freqMid1 - $freqMin) / $range) / 2);
		
		$this->view->soundFilePath = $soundFileView;
		$this->view->imageFilePath = $spectrogramImagePath;
		$this->view->soundID = $this->soundID;
		$this->view->selectionDuration = $timeMax - $timeMin;
		$this->view->freqMid1 = $freqMid1;
		$this->view->freqMid2 = $freqMid2;
		$this->view->freqMinHeight = $freqMinHeight;
		$this->view->freqMaxHeight = $freqMaxHeight;
		$this->view->freqMid1Height = $freqMid1Height;
		$this->view->freqMid2Height = $freqMid2Height;
		$this->view->samplingRate = $samplingRate;
		$this->view->numChannels = $channels;
		$this->view->colID = $soundData["ColID"];
		$this->view->userID = Auth::getUserID();

		$this->setSoundTags($timeMin, $timeMax, $freqMin, $freqMax, $spectrogramWidth);	
		$this->setTime($timeMax, $timeMin);	
		$this->setViewPort($samplingRate, $currentChannel, $originalWavFilePath);

	}
	
	private function setTime($timeMax, $timeMin){
		//Time Scale
		$this->view->timeScale = "";
		
		$step = 10.85;
		$dur = $timeMax - $timeMin;
		$dur_ea = $dur / $step;
		$second1 = $timeMin;

		for($i = 0; $i < 11; $i++){
			if($i > 0)
				$second1 = $second1 + $dur_ea;
			$second = round($second1, 0);
			if ($dur_ea < 1){
				$second = round($second1, 1);
			}
			$this->view->timeScale .= "<div class='col-sm-1'><img src='assets/images/vert_line.png'> $second s</div>";
		}
		
		//Player Timer
		$this->view->playerSeconds = $timeMin > 0 ? round($timeMin) : 0;
		$seconds = round($this->view->selectionDuration, 0);
		$this->view->playerDuration = $seconds." s";
	}
	
	private function setViewPort($samplingRate, $channel, $fileName){
		$this->view->viewPort = Utils::generateThumbnail($samplingRate, $this->view->freqMin, $this->view->freqMax, $this->view->timeMin, $this->view->timeMax, $fileName, $channel, $this->view->duration);
	}	
	
	private function setSoundTags($viewTimeMin, $viewTimeMax, $viewFreqMin, $viewFreqMax, $specWidth){	
		$lastPos=0;
		$classHide="";
		
		$soundTagModel = new SoundTag();
		if(!$this->showTags)
		  $classHide = "hidden";
		  
		$viewPermission = false;  
		$reviewPermission = false;
		
		if(!Auth::isUserAdmin()){
			$userPerm = new UserPermission(); 
			$perm = $userPerm->getUserColPermission(Auth::getUserLoggedID(), $this->colID); 
			if(empty($perm))
				$perm = -1;
			$_SESSION["user_col_permission"] = $perm;
						
			$permission = new Permission();		
			$reviewPermission = $permission->isReviewPermission($perm);
			$viewPermission = $permission->isViewPermission($perm);
		}  	
		
		if(Auth::isUserAdmin() || $reviewPermission || $viewPermission)  
			$tags = $soundTagModel->getSoundTags($this->soundID);
		else 
			$tags = $soundTagModel->getSoundTags($this->soundID, Auth::getUserLoggedID());  			
				
		if (!empty($tags)) {
			$viewTotalTime = $viewTimeMax - $viewTimeMin;
			$viewFreqRange = $viewFreqMax - $viewFreqMin;
			
			$i = count($tags);	
			$user = new User();					
			foreach($tags as $key => $value) {
				$tagID = $value[SoundTag::TAG_ID];
				$tagTimeMax = $value[SoundTag::TIME_MAX];
				$tagTimeMin = $value[SoundTag::TIME_MIN];
				$tagFreqMin = $value[SoundTag::FREQ_MIN];
				$tagFreqMax = $value[SoundTag::FREQ_MAX];
				$tagUser = $value[SoundTag::USER_ID];
				$animalName = $value["AnimalName"];
				$tagStyle = !isset($value[SoundTag::CALL_DISTANCE]) && empty($value[SoundTag::DISTANCE_NOT_ESTIMABLE]) ? "tag-orange" : "";
				$tagStyle = empty($value["reviews"]) ? $tagStyle ." tag-dashed" : $tagStyle;
				
				if(empty($userTagColor = $user->getTagColor($tagUser)))
					$userTagColor = self::DEFAULT_TAG_COLOR;
				
				#Only show if some part of the mark is inside the current window
				if ($tagTimeMin < $viewTimeMax && $tagTimeMax > $viewTimeMin && $tagFreqMin < $viewFreqMax && $tagFreqMax > $viewFreqMin){
					//Time and freq calculations to draw the boxes of tags
					

					if ($tagTimeMax > $viewTimeMax) 
						$tagTimeMax = $viewTimeMax;

					if ($tagTimeMin < $viewTimeMin) {
						$time_i = 0;
						$tagTimeMin = $viewTimeMin;
					} else 
						$time_i=(($tagTimeMin-$viewTimeMin)/$viewTotalTime) * $specWidth;

					if ($tagFreqMax > $viewFreqMax) {
						$freq_i = 0;
						$tagFreqMax = $viewFreqMax;
					} else
						$freq_i = ((($viewFreqRange + $viewFreqMin)-$tagFreqMax)/$viewFreqRange) * SPECTROGRAM_HEIGHT;

					if ($tagFreqMin < $viewFreqMin){
						$freq_w = SPECTROGRAM_HEIGHT - $freq_i;
					} else
						$freq_w = (($tagFreqMax-$tagFreqMin)/$viewFreqRange) * SPECTROGRAM_HEIGHT;

					$time_w=(($tagTimeMax-$tagTimeMin)/$viewTotalTime) * $specWidth;
			
					$pos = $i + 800;
					$lastPos = $pos++;
					$this->view->soundTags .= "<div class='tag-controls $tagStyle' id='$tagID' style='z-index:".$pos."; border-color: $userTagColor; left: " . 
					$time_i . "px; top: " . $freq_i . "px; height: ". $freq_w . "px; width: " . $time_w . "px; ". $classHide. "'></div>";
					$this->view->soundTags .= "<div class='panel panel-default panel-tag' hidden><div class='panel-heading'>$animalName</div>";
					$this->view->soundTags .= "<div class='panel-body'><div class='btn-group no-wrap' role='group'>";
					$this->view->soundTags .= "<a href='ajaxcallmanager.php?class=SoundTag&action=edit&id=$tagID' class='btn btn-primary btn-sm tag' title='Edit Tag'>";
					$this->view->soundTags .="<span class='glyphicon glyphicon-edit' aria-hidden='true'></span></a>";
					$this->view->soundTags .="<a href='#' onclick='return false;' class='btn btn-primary btn-sm zoom-tag' title='Zoom Tag'><span class='glyphicon glyphicon-zoom-in' aria-hidden='true'></span></a>";
					$this->view->soundTags .= "<a href='#' onclick='return false;' id='est_$tagID' type='button' class='btn btn-primary btn-sm estimate-distance' title='Estimate Call Distance'><span class='glyphicon glyphicon-bullhorn' aria-hidden='true'></span></a>";
					$this->view->soundTags .= "</div></div></div>";
					$i--;
				}
			}
		}	
	}
}
