<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Entity\PlayLog;
use Hybridars\BioSounds\Utils\Auth;

class PlayLogController
{
    /**
     * PlayLogController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
		if(!Auth::isUserLogged()){
			throw new \Exception(ERROR_NOT_LOGGED);
		}
    }

    /**
     * @return int|null
     * @throws \Exception
     */
    public function save()
    {
		if (!Auth::isUserLogged()) {
			throw new \Exception(ERROR_NOT_LOGGED);
		}

		$data[PlayLog::RECORDING_ID] = filter_var($_POST['recordingId'], FILTER_SANITIZE_NUMBER_INT);
		$data[PlayLog::USER_ID] = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
		$data[PlayLog::START_TIME] = date('Y-m-d H:i:s', (int)filter_var($_POST['startTime'], FILTER_SANITIZE_STRING));
		$data[PlayLog::STOP_TIME] = date('Y-m-d H:i:s', (int)filter_var($_POST['stopTime'], FILTER_SANITIZE_STRING));

		return (new PlayLog())->insert($data);
	}
}
