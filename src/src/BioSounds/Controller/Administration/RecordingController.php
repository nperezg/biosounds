<?php

namespace BioSounds\Controller\Administration;

use BioSounds\Controller\BaseController;
use BioSounds\Entity\Recording;
use BioSounds\Entity\Sensor;
use BioSounds\Entity\Site;
use BioSounds\Exception\EmptyIdException;
use BioSounds\Exception\ForbiddenException;
use BioSounds\Provider\CollectionProvider;
use BioSounds\Provider\RecordingProvider;
use BioSounds\Provider\SpectrogramProvider;
use BioSounds\Provider\SoundProvider;
use BioSounds\Provider\SoundTypeProvider;
use BioSounds\Utils\Auth;

class RecordingController extends BaseController
{
    const ITEMS_PAGE = 15;
    const SECTION_TITLE = 'Recordings';

    /**
     * @param int|null $id
     * @param int $page
     * @return mixed
     * @throws \Exception
     */
    public function show(int $id = null, int $page = 1)
    {
        if (!Auth::isUserAdmin()){
            throw new ForbiddenException();
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
        $pages = $recordingNum > 0 ? ceil($recordingNum / self::ITEMS_PAGE) : 1;

        return $this->twig->render('administration/recordings.html.twig', [
            'colId' => $colId,
            'recordings' => $recordings,
            'sites' => (new Site())->getBasicList(),
            'sensors' => (new Sensor())->getBasicList(),
            'soundTypes' => (new SoundTypeProvider())->getList(),
            'currentPage' => ($page > $pages) ?: $page,
            'pages' => $pages,
        ]);
    }

    /**
     * @return bool|int|null
     * @throws \Exception
     */
    public function save()
    {
        if (!Auth::isUserAdmin()) {
            throw new ForbiddenException();
        }

        $data = [];
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

        if (isset($data["itemID"])) {
            (new RecordingProvider())->update($data);

            return json_encode([
                'errorCode' => 0,
                'message' => 'Recording updated successfully.',
            ]);
        }
	}

    /**
     * @param int $id
     * @return false|string
     * @throws \Exception
     */
	public function delete(int $id)
	{
        if (!Auth::isUserAdmin()){
            throw new ForbiddenException();
        }

        if (empty($id)) {
            throw new EmptyIdException();
        }

        $recordingProvider = new RecordingProvider();
        $recording = $recordingProvider->get($id);

        $fileName = $recording[Recording::FILENAME];
        $colId = $recording[Recording::COL_ID];
        $dirID = $recording[Recording::DIRECTORY];

        $soundsDir = "sounds/sounds/$colId/$dirID/";
        $imagesDir = "sounds/images/$colId/$dirID/";

        unlink($soundsDir . $fileName);
        //Check if there are images
        $images = (new SpectrogramProvider())->getListInRecording($id);

        foreach ($images as $image) {
            unlink($imagesDir . $image->getFilename());
        }

        $wavFileName = substr($fileName, 0, strrpos($fileName, '.')) . '.wav';
        if (is_file($soundsDir . $wavFileName)) {
            unlink($soundsDir . $wavFileName);
        }

        $recordingProvider->delete($id);

        if (!empty($recording[Recording::SOUND_ID])) {
            (new SoundProvider())->delete($recording[Recording::SOUND_ID]);
        }

        return json_encode([
            'errorCode' => 0,
            'message' => 'Recording deleted successfully.',
        ]);
	}
}
