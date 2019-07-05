<?php


namespace BioSounds\Exception\Database;


class NotFoundException extends \Exception
{
    const MESSAGE = 'Entry with id %s not found.';

    /**
     * NotFoundException constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        parent::__construct(sprintf($this::MESSAGE, $id));
    }
}
