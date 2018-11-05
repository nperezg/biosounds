<?php

namespace Hybridars\BioSounds\Exception;

class RenderException extends \RuntimeException
{
    const MESSAGE = 'Rendering failed %s';

    public function __construct(string $message, int $code = null)
    {
        parent::__construct(sprintf($this::MESSAGE, $message), $code);
    }
}