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
    public function getListOrderById(): array
    {
        return $this->getList('collection_id');
    }

    /**
     * @param string $order
     * @return Collection[]
     * @throws \Exception
     */
    public function getList(string $order = 'name'): array
    {
        $data = [];
        $this->database->prepareQuery('SELECT * FROM collection ORDER BY :order');

        $result = $this->database->executeSelect([':order' => $order]);
        foreach($result as $item) {
            $data[] = (new Collection())
                ->setId($item['collection_id'])
                ->setName($item['name'])
                ->setAuthor($item['author'])
                ->setSource($item['source'])
                ->setCitation($item['citation'])
                ->setUrl($item['url'])
                ->setNote($item['note']);
        }

        return $data;
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
            ->setAuthor($result['author'])
            ->setSource($result['source'])
            ->setCitation($result['citation'])
            ->setUrl($result['url'])
            ->setNote($result['note'])
            ->setView($result['view']);
    }
}
