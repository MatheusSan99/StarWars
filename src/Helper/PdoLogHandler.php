<?php

namespace StarWars\Helper;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use PDO;
use PDOStatement;

class PdoLogHandler extends AbstractProcessingHandler
{
    private bool $initialized = false;
    private PDO $pdo;
    private PDOStatement $statement;

    public function __construct(PDO $pdo, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->pdo = $pdo;
        parent::__construct($level, $bubble);
    }

    protected function write($record): void
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        $this->statement->execute([
            'channel' => $record['channel'],
            'level' => $record['level'],
            'message' => $record['message'],
            'time' => $record['datetime']->getTimestamp(),
        ]);
    }

    private function initialize(): void
    {
        $this->statement = $this->pdo->prepare(
            'INSERT INTO monolog (channel, level, message, time) VALUES (:channel, :level, :message, :time)'
        );

        $this->initialized = true;
    }
}
