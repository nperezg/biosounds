<?php

namespace BioSounds\Exception\File;

/**
 * Class FileQueueNotFoundException
 * @package BioSounds\Exception\File
 */
class FileQueueNotFoundException extends \Exception
{
    const MESSAGE = 'File with id %s was not found in the database.';

    /**
     * FileQueueNotFoundException constructor.
     * @param int $fileId
     */
    public function __construct(int $fileId)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileId), 404);
    }
}