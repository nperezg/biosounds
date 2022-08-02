<?php

namespace BioSounds\Provider;

use BioSounds\Entity\Collection;
use BioSounds\Exception\Database\NotFoundException;

class CollectionProvider extends BaseProvider
{
    /**
     * @return Collection[]
     * @throws \Exception
     */
    public function getCollectionPages(int $limit, int $offSet): array
    {
        $this->database->prepareQuery(
            "SELECT * FROM collection ORDER BY collection_id LIMIT :limit OFFSET :offset"
        );

        $result = $this->database->executeSelect([
            ':limit' => $limit,
            ':offset' => $offSet,
        ]);

        $data = [];
        foreach ($result as $item) {
            $data[] = (new Collection())
                ->setId($item['collection_id'])
                ->setName($item['name'])
                ->setUserId($item['user_id'])
                ->setDoi($item['doi'])
                ->setNote($item['note'])
                ->setProject($item['project_id'])
                ->setCreationDate($item['creation_date'])
                ->setPublic($item['public'])
                ->setView($item['view']);
        }
        return $data;
    }

    /**
     * @param string $order
     * @return Collection[]
     * @throws \Exception
     */
    public function getList(string $order = 'name'): array
    {
        $data = [];
        $this->database->prepareQuery("SELECT * FROM collection ORDER BY $order");

        $result = $this->database->executeSelect();

        foreach ($result as $item) {
            $data[] = (new Collection())
                ->setId($item['collection_id'])
                ->setName($item['name'])
                ->setUserId($item['user_id'])
                ->setDoi($item['doi'])
                ->setNote($item['note'])
                ->setProject($item['project_id'])
                ->setView($item['view']);
        }

        return $data;
    }

    public function countCollections(): int
    {
        $this->database->prepareQuery(
            "SELECT count(collection_id) AS num FROM collection"
        );

        if (empty($result = $this->database->executeSelect())) {
            return 0;
        }
        return $result[0]['num'];
    }

    /**
     * @param int $id
     * @return Collection|null
     * @throws \Exception
     */
    public function get(int $id): ?Collection
    {
        $this->database->prepareQuery('SELECT * FROM collection WHERE collection_id = :id');

        if (empty($result = $this->database->executeSelect([':id' => $id]))) {
            throw new NotFoundException($id);
        }

        $result = $result[0];

        return (new Collection())
            ->setId($result['collection_id'])
            ->setName($result['name'])
            ->setUserId($result['user_id'])
            ->setDoi($result['doi'])
            ->setNote($result['note'])
            ->setView($result['view']);
    }


    /**
     * @param string $order
     * @return Collection[]
     * @throws \Exception
     */
    public function getAccessedList(int $userId): array
    {
        $data = [];
        $this->database->prepareQuery('SELECT * FROM collection WHERE collection_id IN ( SELECT up.collection_id FROM user_permission up, permission p WHERE up.permission_id = p.permission_id AND (p.name = "Access" OR p.name = "View" OR p.name = "Review") AND up.user_id = :userId) ORDER BY name');

        $result = $this->database->executeSelect([':userId' => $userId]);

        foreach ($result as $item) {
            $data[] = (new Collection())
                ->setId($item['collection_id'])
                ->setName($item['name'])
                ->setUserId($item['user_id'])
                ->setDoi($item['doi'])
                ->setNote($item['note'])
                ->setProject($item['project_id']);
        }

        return $data;
    }
}
