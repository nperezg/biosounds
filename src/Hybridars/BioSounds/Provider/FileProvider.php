<?php

namespace Hybridars\BioSounds\Provider;

use Hybridars\BioSounds\Database\Database;
use Hybridars\BioSounds\Entity\File;

class FileProvider
{
    /**
     * @param File $file
     * @return int|null
     * @throws \Exception
     */
    public function insert(File $file)
    {
        $query = 'INSERT INTO FilesUpload (FullPath, OriginalFilename, Date, Time, SiteID, ColID, DirID, ';
        $query .= 'SensorID, SoundID, user_id, species_id, sound_type_id, subtype, rating) ';
        $query .= 'VALUES (:filePath, :filename, :date, :time, :site, :collection, :directory, :sensor, ';
        $query .= ':recording, :user, :species, :soundType, :subtype, :rating)';

        Database::prepareQuery($query);
        return Database::executeInsert($file->getDatabaseValues());
    }

    /**
     * @param int $fileId
     * @return null|File
     * @throws \Exception
     */
    public function get(int $fileId): ?File
    {
        Database::prepareQuery("SELECT * FROM FilesUpload WHERE files_upload_id = $fileId");

        if (!empty($result = Database::executeSelect())) {
            $result = $result[0];
            return (new File())
                ->setPath($result['FullPath'])
                ->setName($result['OriginalFilename'])
                ->setDate($result['Date'])
                ->setTime($result['Time'])
                ->setSite($result['SiteID'])
                ->setCollection($result['ColID'])
                ->setDirectory($result['DirID'])
                ->setSensor($result['SensorID'])
                ->setId($result['files_upload_id'])
                ->setUser($result['user_id'])
                ->setSpecies($result['species_id'])
                ->setSoundType($result['sound_type_id'])
                ->setSubtype($result['subtype'])
                ->setRating($result['rating']);
        }
        return null;
    }

    /**
     * @param File $file
     * @throws \Exception
     */
    public function update(File $file)
    {
        $query = 'UPDATE FilesUpload SET ';
        $query .= "ErrorMessage = '" . $file->getErrorMessage() . "', ";
        $query .= "status = " . $file->getStatus() . " ";

        if (!empty($file->getRecording())) {
            $query .= ", SoundID = " . $file->getRecording() . " ";
        }

        $query .= "WHERE files_upload_id = " . $file->getId();
        Database::prepareQuery($query);
        Database::executeUpdate();
    }
}