<?php

namespace BioSounds\Exception;


class NotAuthenticatedException extends \Exception
{
    const MESSAGE = 'User not authenticated. Please, log in.';

    public function __construct()
    {
        parent::__construct($this::MESSAGE);
    }
}