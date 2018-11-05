<?php

namespace Hybridars\BioSounds\Provider;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\SoundImage;

class SoundImageProvider
{
    /**
     * @param SoundImage $soundImage
     * @return int|null
     * @throws \Exception
     */
    public function insert(SoundImage $soundImage)
    {
        $query = 'INSERT INTO SoundsImages (recording_id, filename, type, palette, SpecMaxFreq) ';
        $query .= 'VALUES (:recordingId, :filename, :type, :palette, :specMaxFreq)';
        Database::prepareQuery($query);
        return Database::executeInsert($soundImage->getDatabaseValues());
    }

    /**
     * @param int $recordingId
     * @param string $imageType
     * @return SoundImage|null
     * @throws \Exception
     */
    public function get(int $recordingId, string $imageType): ?SoundImage
    {
        $query = 'SELECT filename FROM SoundsImages WHERE type = :type AND ';
        $query .= 'recording_id = :recordingId';

        Database::prepareQuery($query);
        if (!empty($result = Database::executeSelect([':recordingId' => $recordingId, ':type' => $imageType]))) {
            return (new SoundImage())
                ->setImageFile($result[0]['filename']);

        }
        return null;
    }

    /**
     * @param int $id
     * @return SoundImage[]
     * @throws \Exception
     */
    public function getListInRecording(int $id): array
    {
        $query = 'SELECT recording_id, filename, type, palette FROM SoundsImages WHERE recording_id = :recordingId';
        Database::prepareQuery($query);

        $data = [];
        $list = Database::executeSelect([':recordingId' => $id]);
        foreach ($list as $item) {
            $data[] = (new SoundImage())
                ->setSoundId($item['recording_id'])
                ->setImageFile($item['filename'])
                ->setColorPalette($item['palette'])
                ->setImageType($item['type']);
        }
        return $data;
    }

    /**
     * @param int $recordingId
     * @return array|int
     * @throws \Exception
     */
    public function deleteByRecording(int $recordingId)
    {
        Database::prepareQuery('DELETE FROM SoundsImages WHERE recording_id = ' . $recordingId);
        return Database::executeDelete();
    }
}