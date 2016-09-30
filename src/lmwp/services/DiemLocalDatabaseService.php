<?php
namespace lmwp\services;

use lmwp\models\DiemLocalDatabase;


class DiemLocalDatabaseService
{
    private $localDatabase;

    public function __construct(string $dbPath)
    {
        $this->localDatabase = new DiemLocalDatabase($dbPath);
    }

    public function isDatabaseOk()
    {
        return $this->localDatabase->getHandler() !== null;
    }

    public function getIndexCount()
    {
        return $this->localDatabase->getIndexCount();
    }
}