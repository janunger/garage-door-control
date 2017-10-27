<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\EndToEnd\ControlSequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\Repository;
use JUIT\GDC\ControlSequence\SequenceItem;
use PDO;
use PHPUnit\DbUnit\Database\Connection;
use PHPUnit\DbUnit\DataSet\DefaultDataSet;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;
use PHPUnit\DbUnit\Operation\Factory;
use PHPUnit\DbUnit\TestCase;

class RepositoryTest extends TestCase
{
    /** @var Repository */
    private $SUT;

    protected function setUp()
    {
        parent::setUp();
        $this->SUT = new Repository($this->getConnection()->getConnection());
    }

    /** @test */
    public function it_fetches_all_sequence_items()
    {
        static::assertEquals(
            [
                new SequenceItem(1, Command::TRIGGER_DOOR()),
                new SequenceItem(2, Command::CANCEL()),
            ],
            $this->SUT->getCommands()
        );
    }

    /**
     * Returns the test database connection.
     *
     * @return Connection
     */
    protected function getConnection()
    {
        $config = require PROJECT_ROOT_DIR . '/etc/config.php';
        $dbName   = $config['database_name'];
        $host     = $config['database_host'];
        $username = $config['database_username'];
        $password = $config['database_password'];

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
        return new FlatXmlDataSet(__DIR__ . '/Repository/seed.xml');
    }

    protected function getTearDownOperation()
    {
        return Factory::TRUNCATE();
    }
}
