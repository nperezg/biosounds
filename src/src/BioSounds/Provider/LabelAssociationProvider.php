<?php

namespace BioSounds\Provider;

use BioSounds\Entity\Label;
use BioSounds\Exception\Database\NotFoundException;

class LabelAssociationProvider extends BaseProvider
{
    /**
     * @return Label[]
     * @throws \Exception
     */
    public function getBasicList(): array
    {
        return $this->getList('label_id');
    }

    /**
     * @param int $recId
     * @param int $loggedUserId
     * @return Label or NULL
     * @throws \Exception
     */
    public function getUserLabel(int $recId, int $loggedUserId): ?Label
    {
        $data = [];
        $query =
            "SELECT l.label_id, l.name, l.creation_date, l.customized 
            FROM label l, label_association la
            WHERE la.label_id = l.label_id 
            AND la.user_id = :user_id
            AND la.recording_id = :recording_id ";

        $this->database->prepareQuery($query);
        $result = $this->database->executeSelect([':recording_id' => $recId, ':user_id' => $loggedUserId]);

        foreach ($result as $item) {
            $data[] = (new Label())
                ->setId($item['label_id'])
                ->setName($item['name'])
                ->setCreationDate($item['creation_date'])
                ->setCustomized($item['customized']);
        }

        // TODO: 
        if (!empty($data)) {
            return $data[0];
        } else {
            // WORKAROUND: 1, 'not analysed'
            return (new Label())->setId(1)->setName('not analysed')->setCustomized(false);
        }
    }

    /**
     * @param string $order
     * @return Label[]
     * @throws \Exception
     */
    private function getList(string $order = 'name'): array
    {
        $data = [];
        $this->database->prepareQuery(
            "SELECT * FROM label ORDER BY $order"
        );

        $result = $this->database->executeSelect();

        foreach ($result as $item) {
            $data[] = (new Label())
                ->setId($item['label_id'])
                ->setName($item['name'])
                ->setCreationDate($item['creation_date'])
                ->setCustomized($item['customized']);
        }

        return $data;
    }

    /**
     * @param int $siteId, $userId
     * @return Site|null
     * @throws \Exception
     */
    public function get(int $lblId): ?Label
    {
        $this->database->prepareQuery('SELECT * FROM site WHERE label_id = :labelId');

        if (empty($result = $this->database->executeSelect([':labelId' => $lblId]))) {
            throw new NotFoundException($lblId);
        }

        $result = $result[0];

        return (new Label())
            ->setId($result['label_id'])
            ->setName($result['name'])
            ->setCreationDate($result['creation_date'])
            ->setCustomized($result['customized']);
    }

    public function setEntry(array $repData)
    {
        if (empty($repData)) {
            return false;
        }

        $values = [];
        foreach ($repData as $key => $value) {
            $values[':' . $key] = $value;
        }

        $this->database->prepareQuery('REPLACE INTO label_association(recording_id, user_id, label_id) 
        VALUES (:recording_id, :user_id, :label_id)');
        return $this->database->executeUpdate($values);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $this->database->prepareQuery('DELETE FROM site WHERE ' . Label::PRIMARY_KEY . ' = :id');
        $this->database->executeDelete([':id' => $id]);
    }
}
