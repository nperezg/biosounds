<?php


namespace BioSounds\Exception;


class ForbiddenException extends \Exception
{
    const MESSAGE = 'Forbidden access.';

    public function __construct()
    {
        parent::__construct($this::MESSAGE, 403);
    }
}
