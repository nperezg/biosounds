<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\SoundListenLog;
use Hybridars\BioSounds\Utils\Auth;

class SoundListenLogController
{
    public function __construct() {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
    }
	
    public function save(){
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
		$data = array();
		$data[SoundListenLog::SOUND_ID] = filter_var($_POST[SoundListenLog::SOUND_ID], FILTER_SANITIZE_NUMBER_INT); 
		$data[SoundListenLog::USER_ID] = filter_var($_POST[SoundListenLog::USER_ID], FILTER_SANITIZE_NUMBER_INT); 
		$data[SoundListenLog::START_TIME] = date("Y-m-d H:i:s", filter_var($_POST[SoundListenLog::START_TIME], FILTER_SANITIZE_STRING)); 
		$data[SoundListenLog::STOP_TIME] = date("Y-m-d H:i:s", filter_var($_POST[SoundListenLog::STOP_TIME], FILTER_SANITIZE_STRING)); 
		
		$soundLog = new SoundListenLog();
		return $soundLog->insertSoundLog($data);
	}
}
