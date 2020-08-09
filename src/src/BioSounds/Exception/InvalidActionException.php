<?php

namespace BioSounds\Exception;

use Exception;

/**
 * Class InvalidActionException
 * @package BioSounds\Exception
 */
class InvalidActionException extends Exception
{
    private const MESSAGE = 'Action %s not found.';

    /**
     * InvalidActionException constructor.
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct(sprintf(self::MESSAGE, $action));
    }
}
