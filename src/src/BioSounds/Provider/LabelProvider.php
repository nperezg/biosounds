<?php

namespace BioSounds\Provider;

use BioSounds\Entity\Label;
use BioSounds\Exception\Database\NotFoundException;

class LabelProvider extends BaseProvider
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

    public function newLabel(string $lblName)
    {
        if (empty($lblName)) {
            return false;
        }

        $this->database->prepareQuery('INSERT INTO label(name, customized, creation_date) 
        VALUES (:name, true, now())');
        return $this->database->executeInsert([':name' => $lblName]);
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
