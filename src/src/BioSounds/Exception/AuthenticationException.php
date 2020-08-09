<?php

namespace BioSounds\Exception;

use Exception;

/**
 * Class AuthenticationException
 * @package BioSounds\Exception
 */
class AuthenticationException extends Exception
{
    private const MESSAGE = 'Invalid username or password, please try again.';

    /**
     * AuthenticationException constructor.
     */
    public function __construct()
    {
        parent::__construct(self::MESSAGE);
    }
}