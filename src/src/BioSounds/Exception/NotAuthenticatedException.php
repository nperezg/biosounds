<?php

namespace BioSounds\Exception;

use Exception;

/**
 * Class NotAuthenticatedException
 * @package BioSounds\Exception
 */
class NotAuthenticatedException extends Exception
{
    private const MESSAGE = 'User not authenticated. Please, log in.';

    /**
     * NotAuthenticatedException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}