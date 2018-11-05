<?php

namespace Hybridars\BioSounds\Provider;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\Sound;

class SoundProvider
{
    /**
     * @param int $id
     * @return Sound|null
     * @throws \Exception
     */
    public function get(int $id): ?Sound
    {
        $query = 'SELECT sound.species_id AS species_id, sound.sound_type_id AS sound_type_id, subtype, ';
        $query .= 'distance, not_estimable_distance, individual_num, uncertain, rating, note, binomial, ';
        $query .= 'sound_type.name as type_name FROM sound LEFT JOIN species ';
        $query .= 'ON sound.species_id = species.species_id LEFT JOIN sound_type ';
        $query .= 'ON sound.sound_type_id = sound_type.sound_type_id WHERE sound_id = :soundId';

        Database::prepareQuery($query);
        if (!empty($result = Database::executeSelect([':soundId' => $id]))) {
            return (new Sound())
                ->setSoundId($id)
                ->setSpecies($result[0]['species_id'])
                ->setSpeciesName($result[0]['binomial'])
                ->setType($result[0]['sound_type_id'])
                ->setTypeName($result[0]['type_name'])
                ->setSubtype($result[0]['subtype'])
                ->setDistance($result[0]['distance'])
                ->setNotEstimableDistance($result[0]['not_estimable_distance'])
                ->setIndividualNum($result[0]['individual_num'])
                ->setUncertain($result[0]['uncertain'])
                ->setRating($result[0]['rating'])
                ->setNote($result[0]['note']);
        }
        return null;
    }

    /**
     * @param Sound $sound
     * @return array|int
     * @throws \Exception
     */
    public function insert(Sound $sound)
    {
        $query = 'INSERT INTO sound (species_id, sound_type_id, subtype, distance, not_estimable_distance, ';
        $query .= 'individual_num, uncertain, rating, note) ';
        $query .= 'VALUES (:species, :type, :subtype, :distance, :notEstimableDistance, :individualNum, ';
        $query .= ':uncertain, :rating, :note)';

        Database::prepareQuery($query);
        return Database::executeInsert($sound->getDatabaseValues());
    }

    /**
     * @param $id
     * @return array|int
     * @throws \Exception
     */
    public function delete($id)
    {
        Database::prepareQuery('DELETE FROM sound WHERE sound_id = :id' );
        return Database::executeDelete([':id' => $id]);
    }
}