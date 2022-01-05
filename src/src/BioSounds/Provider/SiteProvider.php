<?php

namespace BioSounds\Provider;

use BioSounds\Entity\Site;
use BioSounds\Exception\Database\NotFoundException;

class SiteProvider extends BaseProvider
{
    /**
     * @return Site[]
     * @throws \Exception
     */
    public function getListOrderById(): array
    {
        return $this->getList('site_id');
    }

    /**
     * @param string $order
     * @return Site[]
     * @throws \Exception
     */
    public function getList(string $order = 'name'): array
    {
        $data = [];
        $this->database->prepareQuery(
            "SELECT * FROM site ORDER BY $order"
        );

        $result = $this->database->executeSelect();

        foreach ($result as $item) {
            $data[] = (new Site())
                ->setId($item['site_id'])
                ->setName($item['name'])
                ->setUserId($item['user_id'])
                ->setCreationDateTime($item['creation_date_time'])
                ->setLongitude($item['longitude_WGS84_dd_dddd'])
                ->setLatitude($item['latitude_WGS84_dd_dddd'])
                ->setGadm1($item['gadm1'])
                ->setGadm2($item['gadm2'])
                ->setGadm3($item['gadm3'])
                ->setCentroId($item['centroid']);
        }

        return $data;
    }

    /**
     * @param int $siteId, $userId
     * @return Site|null
     * @throws \Exception
     */
    public function get(int $siteId, int $userId): ?Site
    {
        $this->database->prepareQuery('SELECT * FROM site WHERE site_id = :siteId and user_id = :userId');

        if (empty($result = $this->database->executeSelect([':siteId' => $siteId, ':userId' => $userId]))) {
            throw new NotFoundException($siteId, $userId);
        }

        $result = $result[0];

        return (new Site())
            ->setId($result['site_id'])
            ->setName($result['name'])
            ->setUserId($result['user_id'])
            ->setCreationDateTime($result['creation_date_time'])
            ->setLongitude($result['longitude_WGS84_dd_dddd'])
            ->setLatitude($result['latitude_WGS84_dd_dddd'])
            ->setGadm1($result['gadm1'])
            ->setGadm2($result['gadm2'])
            ->setGadm3($result['gadm3'])
            ->setCentroId($result['centroid']);
    }

    /**
     * @param int $id
     * @throws \Exception
     */
    public function delete(int $id): void
    {
        $this->database->prepareQuery('DELETE FROM site WHERE ' . Site::PRIMARY_KEY . ' = :id');
        $this->database->executeDelete([':id' => $id]);
    }
}
