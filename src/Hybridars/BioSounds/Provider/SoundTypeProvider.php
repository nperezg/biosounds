<?php

namespace Hybridars\BioSounds\Provider;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\SoundType;

class SoundTypeProvider
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getList(): array
    {
        $list = [];

        Database::prepareQuery('SELECT sound_type_id, name FROM sound_type');

        if (!empty($result = Database::executeSelect())) {
            foreach ($result as $soundType) {
                $list[] = (new SoundType())
                    ->setSoundTypeId($soundType['sound_type_id'])
                    ->setName($soundType['name']);
            }
        }
        return $list;
    }
}
