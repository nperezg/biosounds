<?php

namespace Hybridars\BioSounds\Exception;


use Throwable;

class AuthenticationException extends \Exception
{
    const MESSAGE = 'Invalid username or password, try again.';

    public function __construct()
    {
        parent::__construct($this::MESSAGE);
    }
}