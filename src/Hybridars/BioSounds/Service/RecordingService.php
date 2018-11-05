<?php

namespace Hybridars\BioSounds\Service;

use Hybridars\BioSounds\Presenter\RecordingListPresenter;
use Hybridars\BioSounds\Provider\RecordingProvider;

class RecordingService
{
    const RECORDING_PATH = 'sounds/sounds/%s/%s/%s';
    const IMAGE_PATH = 'sounds/images/%s/%s/%s';

    /**
     * @param int $colId
     * @param int $limit
     * @param int $offSet
     * @param array $filter
     * @return RecordingListPresenter[]
     * @throws \Exception
     */
    public function getListWithImages(int $colId, int $limit, int $offSet, array $filter): array
    {
        $result = [];
        $list = (new RecordingProvider())->getListByCollection($colId, $limit, $offSet, $filter);

        $soundImageService = new SoundImageService();

        foreach($list as $item) {
            $recordingListPresenter = new RecordingListPresenter();
            $recordingListPresenter->setRecording($item);

            $recordingListPresenter->setPlayerImage('assets/images/notready-small.png');
            if (!empty($playerImage = $soundImageService->getPlayerImage($item->getId()))
                && is_file(  sprintf(
                    self::IMAGE_PATH,
                    $item->getCollection(),
                    $item->getDirectory(),
                    $playerImage->getImageFile()
                ))
            ) {
                $recordingListPresenter->setPlayerImage(
                    sprintf(
                        self::IMAGE_PATH,
                        $item->getCollection(),
                        $item->getDirectory(),
                        $playerImage->getImageFile()
                    )
                );
            }

            $recordingListPresenter->setSmallImage('assets/images/notready-small.png');
            if (!empty($smallImage = $soundImageService->getSmallImage($item->getId()))
                && is_file(  sprintf(
                    self::IMAGE_PATH,
                    $item->getCollection(),
                    $item->getDirectory(),
                    $smallImage->getImageFile()
                ))
            ) {
                $recordingListPresenter->setSmallImage(
                    sprintf(
                        self::IMAGE_PATH,
                        $item->getCollection(),
                        $item->getDirectory(),
                        $smallImage->getImageFile()
                    )
                );
            }

            $playerRecording = sprintf(
                self::RECORDING_PATH,
                $item->getCollection(),
                $item->getDirectory(),
                substr($item->getFileName(), 0, strripos($item->getFileName(), '.')) . '.wav'
            );

            $recordingListPresenter->setPlayerRecording($playerRecording);

            if (!is_file($playerRecording)) {
                $recordingListPresenter->setPlayerRecording(
                    sprintf(
                        self::RECORDING_PATH,
                        $item->getCollection(),
                        $item->getDirectory(),
                        $item->getFileName()
                    )
                );
            }

            $result[] = $recordingListPresenter;
        }
        return $result;
    }
}
