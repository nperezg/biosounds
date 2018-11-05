<?php

namespace Hybridars\BioSounds\Exception\Database;

/**
 * Class QueryEmptyException
 * @package Hybridars\BioSounds\Exception\Database
 */
class QueryEmptyException extends DatabaseException
{
    const MESSAGE = 'There is no query to execute.';

    /**
     * QueryEmptyException constructor.
     * @param int $errorCode
     */
    public function __construct(int $errorCode)
    {
        parent::__construct($this::MESSAGE, $errorCode);
    }
}
