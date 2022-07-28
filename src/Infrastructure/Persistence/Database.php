<?php

namespace App\Infrastructure\Persistence;

use PDO;

class Database
{
    protected $connection;

    public function __construct(PDO $db)
    {
        $this->connection = $db;
    }

    protected function getConnection(): PDO
    {
        return $this->connection;
    }
}
