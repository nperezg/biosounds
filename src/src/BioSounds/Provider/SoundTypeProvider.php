<?php

namespace BioSounds\Provider;

use BioSounds\Entity\SoundType;

class SoundTypeProvider extends BaseProvider
{
    /**
     * @return array
     * @throws \Exception
     */
    public function getList(): array
    {
        $list = [];

        $this->database->prepareQuery('SELECT sound_type_id, name FROM sound_type');

        if (!empty($result = $this->database->executeSelect())) {
            foreach ($result as $soundType) {
                $list[] = (new SoundType())
                    ->setSoundTypeId($soundType['sound_type_id'])
                    ->setName($soundType['name']);
            }
        }
        return $list;
    }
}
