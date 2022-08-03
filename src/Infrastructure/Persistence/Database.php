<?php

namespace App\Infrastructure\Persistence;

use PDO;

class Database
{
    protected $connection;

    public function __construct(PDO $db)
    {
        $this->connection = $db;
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    protected function getConnection(): PDO
    {
        return $this->connection;
    }
}
