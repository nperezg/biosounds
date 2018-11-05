<?php

namespace Hybridars\BioSounds\Controller;

use Hybridars\BioSounds\Classes\BaseController;
use Hybridars\BioSounds\Entity\Recording;
use Hybridars\BioSounds\Entity\Collection;
use Hybridars\BioSounds\Entity\Sensor;
use Hybridars\BioSounds\Entity\Site;
use Hybridars\BioSounds\Entity\PlayLog;
use Hybridars\BioSounds\Entity\SoundImage;
use Hybridars\BioSounds\Entity\Tag;
use Hybridars\BioSounds\Provider\CollectionProvider;
use Hybridars\BioSounds\Provider\RecordingProvider;
use Hybridars\BioSounds\Provider\SoundImageProvider;
use Hybridars\BioSounds\Provider\SoundProvider;
use Hybridars\BioSounds\Provider\SoundTypeProvider;
use Hybridars\BioSounds\Utils\Auth;

class RecordingManagerController extends BaseController
{
    const ITEMS_PAGE = 15;

    /**
     * RecordingManagerController constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
		if (!Auth::isUserAdmin()){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
    }

    /**
     * @param int|null $id
     * @param int $page
     * @return mixed
     * @throws \Exception
     */
    public function show(int $id = null, int $page = 1)
    {
        if (!Auth::isUserAdmin()){
            throw new \Exception(ERROR_NO_ADMIN);
        }

        if (isset($_POST['colId'])) {
            $colId = filter_var($_POST['colId'], FILTER_SANITIZE_STRING);
        }

        if (!empty($id)) {
            $colId = $id;
        }

        $collections = (new CollectionProvider())->getList();
        if (empty($colId)) {
            $colId = $collections[0]->getId();
        }

        $recordingProvider = new RecordingProvider();
        $recordings = $recordingProvider->getListByCollection(
            $colId,
            $this::ITEMS_PAGE,
            $this::ITEMS_PAGE * ($page - 1)
        );

        $recordingNum = $recordingProvider->countAllByCollection($colId);
        $pages = $recordingNum > 0 ? ceil($recordingNum / self::ITEMS_PAGE) : 0;

        return $this->render('administration/recordings.html.twig', [
            'collections' => $collections,
            'colId' => $colId,
            'recordings' => $recordings,
            'sites' => (new Site())->getBasicList(),
            'sensors' => (new Sensor())->getBasicList(),
            'soundTypes' => (new SoundTypeProvider())->getList(),
            'currentPage' => ($page > $pages) ?: $page,
            'pages' => $pages,
            'base_url' => APP_URL,
        ]);
    }

    /**
     * @return bool|int|null
     * @throws \Exception
     */
    public function save()
    {
		if (!Auth::isUserAdmin()) {
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		$data = array();
		foreach($_POST as $key => $value){
			if(strpos($key, "_")){
				$type = substr($key, strripos($key, "_") + 1, strlen($key));
				$key = substr($key, 0, strripos($key, "_"));
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

		$soundProvider = new RecordingProvider();

		if (isset($data["itemID"])) {
            return $soundProvider->update($data);
        }
		else if($soundProvider->insert($data) > 0) {
            header("Location: " . APP_URL . "/admin/recordings");
		}
	}

    /**
     * @return array
     * @throws \Exception
     */
	public function delete(): array
	{
		if (!Auth::isUserAdmin() || !isset($_POST["id"])){
			throw new \Exception(ERROR_NO_ADMIN); 
		}
		
		$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);

        $recordingProvider = new RecordingProvider();
		$recording = $recordingProvider->get($id);

		$fileName = $recording[Recording::FILENAME];
        $colId = $recording[Recording::COL_ID];
		$dirID = $recording[Recording::DIRECTORY];

		$soundsDir = ABSOLUTE_DIR . "sounds/sounds/$colId/$dirID/";
		$imagesDir = ABSOLUTE_DIR . "sounds/images/$colId/$dirID/";

        unlink($soundsDir . $fileName);
        //Check if there are images
        $images = (new SoundImageProvider())->getListInRecording($id);

        foreach ($images as $image) {
            unlink($imagesDir . $image->getImageFile());
        }

        $wavFileName = substr($fileName, 0, strrpos($fileName, '.')) . '.wav';
        if (is_file($soundsDir . $wavFileName)) {
            unlink($soundsDir . $wavFileName);
        }

        $recordingProvider->delete($id);

        if (!empty($recording[Recording::SOUND_ID])) {
            (new SoundProvider())->delete($recording[Recording::SOUND_ID]);
        }

		return ['message' => 'The recording has been successfully deleted.'];
	}
}
