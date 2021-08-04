<?php

namespace BioSounds\Provider;

use BioSounds\Database\Database;

class BaseProvider
{
    /*
     * @var Database
     */
    protected $database;

    /**
     * BaseProvider constructor.
     */
    public function __construct()
    {
        $this->database = new Database(DRIVER, HOST, DATABASE, USER, PASSWORD);
    }
}