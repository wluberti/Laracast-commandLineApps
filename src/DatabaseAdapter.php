<?php
/**
 * Created by PhpStorm.
 * User: wouter
 * Date: 17-6-17
 * Time: 13:04
 */

namespace Acme;

use PDO;

class DatabaseAdapter
{
    protected $connection;

    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    public function fetchALl($tableName)
    {
        return $this->connection->query('SELECT * FROM ' . $tableName)->fetchAll();
    }

    public function query($sql, $parameters)
    {
        return $this->connection->prepare($sql)->execute($parameters);
    }
}
