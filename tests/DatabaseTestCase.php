<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests;

use PDO;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\TestCase;

class DatabaseTestCase extends TestCase
{
    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    protected function getConnection()
    {
        $config   = require PROJECT_ROOT_DIR . '/etc/config.php';
        $dbName   = $config['database']['name'];
        $host     = $config['database']['host'];
        $username = $config['database']['username'];
        $password = $config['database']['password'];

        $pdo = new PDO(
            "mysql:dbname={$dbName};host={$host}",
            $username,
            $password,
            [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            ]
        );

        return $this->createDefaultDBConnection($pdo, $dbName);
    }

    /**
     * Returns the test dataset.
     *
     * @return DefaultDataSet
     */
    protected function getDataSet()
    {
        return new DefaultDataSet();
    }

    protected function getTearDownOperation()
    {
        return Factory::TRUNCATE();
    }
}
