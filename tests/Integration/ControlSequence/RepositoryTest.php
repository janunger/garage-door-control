<?php

declare(strict_types=1);

namespace JUIT\GDC\Tests\Integration\ControlSequence;

use JUIT\GDC\ControlSequence\Command;
use JUIT\GDC\ControlSequence\Repository;
use JUIT\GDC\ControlSequence\SequenceItem;
use JUIT\GDC\Tests\DatabaseTestCase;
use PHPUnit\DbUnit\DataSet\FlatXmlDataSet;

class RepositoryTest extends DatabaseTestCase
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
            $this->SUT->getSequence()
        );
    }

    /** @test */
    public function it_deletes_a_single_command()
    {
        $commands = $this->SUT->getSequence();

        $this->SUT->delete($commands[0]);

        $expected = $this
            ->createFlatXMLDataSet(__DIR__ . '/Repository/it_deletes_a_single_command.xml')
            ->getTable('command_queue');
        $actual   = $this->getConnection()->createQueryTable('command_queue', 'SELECT id, command FROM command_queue');
        static::assertTablesEqual($expected, $actual);
    }

    protected function getDataSet()
    {
        return new FlatXmlDataSet(__DIR__ . '/Repository/seed.xml');
    }
}
