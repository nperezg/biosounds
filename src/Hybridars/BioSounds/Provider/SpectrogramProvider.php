<?php

namespace Hybridars\BioSounds\Provider;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\Spectrogram;

class SpectrogramProvider
{
    /**
     * @param Spectrogram $spectrogram
     * @return array|int
     * @throws \Exception
     */
    public function insert(Spectrogram $spectrogram)
    {
        $query = 'INSERT INTO spectrogram (recording_id, filename, type, max_frequency) ';
        $query .= 'VALUES (:recordingId, :filename, :type, :maxFrequency)';
        Database::prepareQuery($query);
        return Database::executeInsert($spectrogram->getDatabaseValues());
    }

    /**
     * @param int $recordingId
     * @param string $imageType
     * @return Spectrogram|null
     * @throws \Exception
     */
    public function get(int $recordingId, string $type): ?Spectrogram
    {
        $query = 'SELECT filename FROM spectrogram WHERE type = :type AND ';
        $query .= 'recording_id = :recordingId';

        Database::prepareQuery($query);
        if (!empty($result = Database::executeSelect([':recordingId' => $recordingId, ':type' => $type]))) {
            return (new Spectrogram())
                ->setFilename($result[0]['filename']);
        }
        return null;
    }

    /**
     * @param int $id
     * @return Spectrogram[]
     * @throws \Exception
     */
    public function getListInRecording(int $id): array
    {
        $query = 'SELECT recording_id, filename, type FROM spectrogram WHERE recording_id = :recordingId';
        Database::prepareQuery($query);

        $data = [];
        $list = Database::executeSelect([':recordingId' => $id]);
        foreach ($list as $item) {
            $data[] = (new Spectrogram())
                ->setRecordingId($item['recording_id'])
                ->setFilename($item['filename'])
                ->setType($item['type']);
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
        Database::prepareQuery('DELETE FROM spectrogram WHERE recording_id = ' . $recordingId);
        return Database::executeDelete();
    }
}