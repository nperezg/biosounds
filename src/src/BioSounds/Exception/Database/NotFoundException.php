<?php


namespace BioSounds\Exception\Database;


class NotFoundException extends \Exception
{
    const MESSAGE = 'Entry with id %s not found.';

    /**
     * NotFoundException constructor.
     * @param int $id1, $id2
     */
    public function __construct(int $id1, $id2 = NULL)
    {
        parent::__construct(sprintf($this::MESSAGE, $id1, $id2));
    }
}
