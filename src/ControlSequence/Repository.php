<?php

declare(strict_types=1);

namespace JUIT\GDC\ControlSequence;

class Repository
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var \PDOStatement
     */
    private $statement;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /** @return SequenceItem[] */
    public function getCommands(): array
    {
        $commands  = [];
        $statement = $this->getPreparedStatement();
        $statement->execute();
        while ($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $commands[] = new SequenceItem(
                (int) $row['id'],
                new Command($row['command'])
            );
        }

        return $commands;
    }

    public function delete(SequenceItem $sequenceItem)
    {

    }

    private function getPreparedStatement(): \PDOStatement
    {
        if (null === $this->statement) {
        $this->statement = $this->pdo->prepare('SELECT * FROM command_queue ORDER BY id ASC');
        }

        return $this->statement;
    }
}
