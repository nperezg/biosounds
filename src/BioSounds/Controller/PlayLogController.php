<?php

namespace BioSounds\Controller;

use BioSounds\Entity\PlayLog;
use BioSounds\Exception\NotAuthenticatedException;
use BioSounds\Utils\Auth;

class PlayLogController
{
    /**
     * @return false|string|void
     * @throws \Exception
     */
    public function save()
    {
        if(!Auth::isUserLogged()){
            return json_encode([
                'errorCode' => 0,
                'message' => 'User not authenticated. Skipping play log saving.'
            ]);
        }

		$data[PlayLog::RECORDING_ID] = filter_var($_POST['recordingId'], FILTER_SANITIZE_NUMBER_INT);
		$data[PlayLog::USER_ID] = filter_var($_POST['userId'], FILTER_SANITIZE_NUMBER_INT);
		$data[PlayLog::START_TIME] = date('Y-m-d H:i:s', (int)filter_var($_POST['startTime'], FILTER_SANITIZE_STRING));
		$data[PlayLog::STOP_TIME] = date('Y-m-d H:i:s', (int)filter_var($_POST['stopTime'], FILTER_SANITIZE_STRING));

		(new PlayLog())->insert($data);

        return json_encode([
            'errorCode' => 0,
        ]);
	}
}
