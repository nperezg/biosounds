<?php

namespace BioSounds\Provider;

use BioSounds\Entity\IndexLog;
use BioSounds\Utils\Auth;

class IndexLogProvider extends BaseProvider
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getList(): array
    {
        $list = [];

        $sql = "SELECT i.*,r.`name` AS recordingName,u.`name` AS userName,it.`name` AS indexName FROM index_log i 
            LEFT JOIN recording r ON r.recording_id = i.recording_id
            LEFT JOIN user u ON u.user_id = i.user_id
            LEFT JOIN index_type it ON it.index_id = i.index_id ";
        if (!Auth::isUserAdmin()) {
            $sql = $sql . ' WHERE i.user_id = ' . Auth::getUserLoggedID();
        }
        $sql = $sql . " ORDER BY i.log_id";
        $this->database->prepareQuery($sql);
        if (!empty($result = $this->database->executeSelect())) {
            foreach ($result as $indexLog) {
                $list[] = (new IndexLog())
                    ->setLogId($indexLog['log_id'])
                    ->setRecordingId($indexLog['recording_id'])
                    ->setRecordingName($indexLog['recordingName'])
                    ->setUserId($indexLog['user_id'])
                    ->setUserName($indexLog['userName'])
                    ->setIndexId($indexLog['index_id'])
                    ->setIndexName($indexLog['indexName'])
                    ->setCoordinates($indexLog['coordinates'])
                    ->setValue($indexLog['value'])
                    ->setParam($indexLog['param'])
                    ->setDate($indexLog['creation_date']);
            }
        }
        return $list;
    }

    public function getIndexLogPages(int $limit, int $offSet): array
    {
        $sql = "SELECT i.*,r.`name` AS recordingName,u.`name` AS userName,it.`name` AS indexName FROM index_log i 
            LEFT JOIN recording r ON r.recording_id = i.recording_id
            LEFT JOIN user u ON u.user_id = i.user_id
            LEFT JOIN index_type it ON it.index_id = i.index_id ";
        if (!Auth::isUserAdmin()) {
            $sql = $sql . 'WHERE i.user_id = ' . Auth::getUserLoggedID();
        }
        $sql = $sql . " ORDER BY i.log_id LIMIT :limit OFFSET :offset";

        $this->database->prepareQuery($sql);
        $result = $this->database->executeSelect([
            ':limit' => $limit,
            ':offset' => $offSet,
        ]);

        $data = [];
        foreach ($result as $item) {
            $data[] = (new IndexLog())
                ->setLogId($item['log_id'])
                ->setRecordingId($item['recording_id'])
                ->setRecordingName($item['recordingName'])
                ->setUserId($item['user_id'])
                ->setUserName($item['userName'])
                ->setIndexId($item['index_id'])
                ->setIndexName($item['indexName'])
                ->setCoordinates($item['coordinates'])
                ->setValue($item['value'])
                ->setParam($item['param'])
                ->setDate($item['creation_date']);
        }
        return $data;
    }

    public function countIndexLogs(): int
    {
        $sql = "SELECT COUNT(*) as num FROM index_log ";
        if (!Auth::isUserAdmin()) {
            $sql = $sql . 'WHERE user_id = ' . Auth::getUserLoggedID();
        }
        $this->database->prepareQuery($sql);
        if (empty($result = $this->database->executeSelect())) {
            return 0;
        }
        return $result[0]['num'];
    }
}
