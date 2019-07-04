<?php

namespace BioSounds\Provider;

use BioSounds\Entity\Recording;

class RecordingProvider extends BaseProvider
{
    /**
     * @return Recording[]
     * @throws \Exception
     */
    public function getList(): array
    {
        $query = 'SELECT recording_id, name, filename, col_id, directory, sensor_id, site_id, ';
        $query .= 'sound_id, file_size, bitrate, channel_num, DATE_FORMAT(file_date, \'%Y-%m-%d\') ';
        $query .= 'AS file_date, DATE_FORMAT(file_time, \'%H:%i:%s\') AS file_time, sampling_rate ';
        $query .= 'FROM recording';

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect();

        $data = [];
        foreach($result as $item) {
            $data[] = (new Recording())
                ->setId($item['recording_id'])
                ->setName($item['name'])
                ->setCollection($item['col_id'])
                ->setDirectory($item['directory'])
                ->setSensor($item['sensor_id'])
                ->setSite($item['site_id'])
                ->setSound($item['sound_id'])
                ->setFileName($item['filename'])
                ->setFileDate($item['file_date'])
                ->setFileTime($item['file_time'])
                ->setFileSize($item['file_size'])
                ->setBitrate($item['bitrate'])
                ->setChannelNum($item['channel_num'])
                ->setSamplingRate($item['sampling_rate']);
        }
        return $data;
    }

    /**
     * @param int $colId
     * @return int
     * @throws \Exception
     */
    public function countAllByCollection(int $colId)
    {
        $query = 'SELECT COUNT(*) AS num FROM ' . Recording::TABLE_NAME . ' WHERE ' . Recording::COL_ID . ' = :colId';

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect([":colId" => $colId]);

        if (empty($result)) {
            return 0;
        }
        return $result[0]['num'];
    }

//    /**
//     * @param $colId
//     * @return int
//     * @throws \Exception
//     */
//    public function countReadyByCollection($colId)
//    {
//        $query = 'SELECT COUNT(*) AS num FROM ' . Recording::TABLE_NAME . ' LEFT JOIN SoundsImages ';
//        $query .= 'ON ' . Recording::TABLE_NAME . '.' . Recording::ID . ' = SoundsImages.recording_id ';
//        $query .= 'WHERE ' . Recording::TABLE_NAME . '.' . Recording::COL_ID . ' = :colId ';
//        $query .= 'AND type=\'spectrogram-small\'';
//
//        $this->database->prepareQuery($query);
//        if (empty($result = $this->database->executeSelect([':colId' => $colId]))) {
//            return 0;
//        }
//        return $result[0]['num'];
//    }

    /**
     * @param int $colId
     * @param array $filter
     * @return int
     * @throws \Exception
     */
    public function countReady(int $colId, array $filter): int
    {
        $values = [':colId' => $colId];

        $query = 'SELECT COUNT(*) AS num FROM ' . Recording::TABLE_NAME . ' LEFT JOIN spectrogram ';
        $query .= 'ON ' . Recording::TABLE_NAME . '.' . Recording::ID . ' = spectrogram.recording_id ';

        if (!empty($filter)) {
            $query .= 'JOIN sound ON recording.sound_id = sound.sound_id ';
        }
        $query .= 'WHERE recording.col_id = :colId AND type=\'spectrogram-small\'';

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $query .= ' AND ' . $key . '= :' . $key;
                $values[':' . $key] = $value;
            }
        }

        $this->database->prepareQuery($query);
        if (empty($result = $this->database->executeSelect($values))) {
            return 0;
        }
        return $result[0]['num'];
    }

//    /**
//     * @param int $colId
//     * @param int $sqlLimit
//     * @param int $sqlOffset
//     * @return array|null
//     * @throws \Exception
//     */
//    public function getListWithImagesByCollection(int $colId, int $sqlLimit, int $sqlOffset): ?array
//    {
//        $query = 'SELECT *, DATE_FORMAT(' . Recording::FILE_DATE . ', \'%d-%b-%Y\') AS ' . Recording::FILE_DATE . ', ';
//        $query .= 'ImageFile ';
//        $query .= 'FROM ' . Recording::TABLE_NAME . ' LEFT JOIN SoundsImages ';
//        $query .= 'ON ' . Recording::TABLE_NAME . '.' . Recording::ID . ' = SoundsImages.recording_id ';
//        $query .= 'WHERE ' . Recording::COL_ID . ' = :colId AND ImageType=\'spectrogram-recording\' ';
//        $query .= 'ORDER BY ' . Recording::NAME . ' LIMIT :sqlLimit OFFSET :sqlOffset';
//
//        $this->database->prepareQuery($query);
//        if (empty($result = $this->database->executeSelect([
//            ':colId' => $colId,
//            ':sqlLimit' => $sqlLimit,
//            ':sqlOffset' => $sqlOffset
//        ]))) {
//            return null;
//        }
//        return $result;
//    }

    /**
     * @param int $colId
     * @param int $limit
     * @param int $offSet
     * @param array|null $filter
     * @return Recording[]
     * @throws \Exception
     */
    public function getListByCollection(int $colId, int $limit, int $offSet, array $filter = null): array
    {
        $values = [
            ':colId' => $colId,
            ':limit' => $limit,
            ':offset' => $offSet,
        ];

        $query = 'SELECT recording_id, name, filename, col_id, directory, sensor_id, site_id, ';
        $query .= 'recording.sound_id, file_size, bitrate, channel_num, duration, ';
        $query .= 'DATE_FORMAT(file_date, \'%Y-%m-%d\') AS file_date, ';
        $query .= 'DATE_FORMAT(file_time, \'%H:%i:%s\') AS file_time, sampling_rate FROM recording ';

        if (!empty($filter)) {
            $query .= 'JOIN sound ON recording.sound_id = sound.sound_id ';
        }
        $query .= 'WHERE col_id = :colId';

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $query .= ' AND ' . $key . '= :' . $key;
                $values[':' . $key] = $value;
            }
        }

        $query .= ' ORDER BY name LIMIT :limit OFFSET :offset';

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect($values);

        $data = [];
        foreach($result as $item) {
            $recording = (new Recording())->createFromValues($item);
            if (!empty($recording->getSound())) {
             $recording->setSoundData((new SoundProvider())->get($recording->getSound()));
            }
            $data[] = $recording;
        }
        return $data;
    }

//
//    /**
//     * @param int $colId
//     * @param int $sqlLimit
//     * @param int $sqlOffset
//     * @return array|null
//     * @throws \Exception
//     */
//    public function getListByCollection(int $colId, int $sqlLimit, int $sqlOffset): ?array
//    {
//        $query = 'SELECT ' . Recording::ID . ', ' . Recording::FILENAME . ', ' . Recording::NAME . ', ';
//        $query .= 'DATE_FORMAT(' . Recording::FILE_DATE . ', \'%Y-%m-%d\') AS ' . Recording::FILE_DATE . ', ';
//        $query .= 'DATE_FORMAT(' . Recording::FILE_TIME . ', \'%H:%i:%s\') AS ' . Recording::FILE_TIME . ' ';
//        $query .= 'FROM ' . Recording::TABLE_NAME . ' WHERE ' . Recording::COL_ID . ' = :colId ';
//        $query .= 'ORDER BY ' . Recording::ID . ' LIMIT :sqlLimit OFFSET :sqlOffset';
//
//        $this->database->prepareQuery($query);
//        if (empty($result = $this->database->executeSelect([
//            ':colId' => $colId,
//            ':sqlLimit' => $sqlLimit,
//            ':sqlOffset' => $sqlOffset
//        ]))) {
//            return null;
//        }
//        return $result;
//    }

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function get(int $id): array
    {
        $query = 'SELECT *, (SELECT filename FROM spectrogram ';
        $query .= 'WHERE ' . Recording::TABLE_NAME . '.' . Recording::ID . ' = spectrogram.recording_id ';
        $query .= 'AND type = \'spectrogram-player\') AS ImageFile ';
        $query .= 'FROM ' . Recording::TABLE_NAME . ' ';
        $query .= 'WHERE ' . Recording::TABLE_NAME . '.' . Recording::ID . ' = :id';

        $this->database->prepareQuery($query);
        if (empty($result = $this->database->executeSelect([':id' => $id]))) {
            throw new \Exception("Recording $id doesn't exist.");
        }
        return $result[0];
    }

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public function getBasic(int $id): array
    {
        $query = 'SELECT * FROM recording WHERE ' . Recording::ID . ' = :id';

        $this->database->prepareQuery($query);
        if (empty($result = $this->database->executeSelect([':id' => $id]))) {
            throw new \Exception("Recording $id doesn't exist.");
        }
        return $result[0];
    }

    /**
     * TODO: This method must substitute getBasic
     * @param int $id
     * @return Recording
     * @throws \Exception
     */
    public function getSimple(int $id): Recording
    {
        $query = 'SELECT * FROM recording WHERE ' . Recording::ID . ' = :id';

        $this->database->prepareQuery($query);
        if (empty($result = $this->database->executeSelect([':id' => $id]))) {
            throw new \Exception("Recording $id doesn't exist.");
        }
        return (new Recording())->createFromValues($result[0]);
    }

    /**
     * @param string $fileHash
     * @return array|null
     * @throws \Exception
     */
    public function getByHash(string $fileHash):? array
    {
        $this->database->prepareQuery('SELECT * FROM ' . Recording::TABLE_NAME . ' WHERE ' . Recording::MD5_HASH . ' = :md5Hash');
        if (empty($result = $this->database->executeSelect([':md5Hash' => $fileHash]))) {
            return null;
        }
        return $result[0];
    }

    /**
     * @param $data
     * @return bool|int|null
     * @throws \Exception
     */
    public function insert($data)
    {
        if (empty($data)) {
            return false;
        }

        $fields = '( ';
        $valuesNames = '( ';
        $values = [];
        end($data);
        $lastKey = key($data);

        foreach ($data as $key => $value) {
            $fields .= $key;
            $valuesNames .= ":".$key;
            $values[":".$key] = $value;
            if($lastKey !== $key){
                $fields .= ", ";
                $valuesNames .= ", ";
            }
        }
        $fields .= ' )';
        $valuesNames .= ' )';

        $this->database->prepareQuery('INSERT INTO ' . Recording::TABLE_NAME . " $fields VALUES $valuesNames");
        return $this->database->executeInsert($values);
    }

    /**
     * @param $data
     * @return bool|int|null
     * @throws \Exception
     */
    public function update($data)
    {
        if (empty($data)) {
            return false;
        }

        $id = $data["itemID"];
        unset($data["itemID"]);
        $fields = [];
        $values = [];

        foreach ($data as $key => $value) {
            $fields[] = $key . " = :".$key;
            $values[":".$key] = $value;
        }

        $values[":id"] = $id;

        $query = 'UPDATE ' . Recording::TABLE_NAME . ' SET ' . implode(", ", $fields) . ' ';
        $query .= 'WHERE ' . Recording::ID. '= :id';

        $this->database->prepareQuery($query);
        return $this->database->executeUpdate($values);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $this->database->prepareQuery('DELETE FROM ' . Recording::TABLE_NAME . ' WHERE ' . Recording::ID . ' = :id' );
        $this->database->executeDelete([':id' => $id]);
    }
}
