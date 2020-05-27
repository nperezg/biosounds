<?php

namespace BioSounds\Exception;


class InvalidActionException extends \Exception
{
    const MESSAGE = 'Action %s not found.';

    public function __construct($action)
    {
        parent::__construct(sprintf($this::MESSAGE, $action));
    }
}
