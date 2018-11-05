<?php

namespace Hybridars\BioSounds\Exception\File;


class ExtensionInvalidException extends \Exception
{
    const MESSAGE = 'File extension %s is not valid for file %s.';

    /**
     * ExtensionInvalidException constructor.
     * @param string $fileExtension
     * @param string $fileName
     */
    public function __construct(string $fileExtension, string $fileName)
    {
        parent::__construct(sprintf($this::MESSAGE, $fileExtension, $fileName));
    }
}