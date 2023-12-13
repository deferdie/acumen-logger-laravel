<?php

namespace AcumenLogger\Tests\Unit;

use AcumenLogger\Loggers\ExceptionLogger;
use Dotenv\Dotenv;
use AcumenLogger\AcumenLogger;
use PHPUnit\Framework\TestCase;

class LoggerTest extends TestCase
{
    /**
     * The logger instance.
     *
     * @var \AcumenLogger\AcumenLogger
     */
    protected $logger;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../../../../../');
        $dotenv->load();


        $this->logger = new AcumenLogger();
        $this->logger->shouldDispatch = false;
    }

    public function testSetLogEntry()
    {
        $this->logger->addLogEntry((object) [
            'message' => 'test',
            'level' => 'info',
            'context' => 'test',
        ]);

        $this->assertEquals(count($this->logger->logs), 1);
    }

    public function testSetSqlQuery()
    {
        $this->logger->addSqlQuery([
            'query' => 'test',
            'bindings' => 'test',
            'time' => 'test',
        ]);

        $this->assertEquals(count($this->logger->sqlQueries), 1);
    }

    public function testAddEvent()
    {
        $this->logger->addEvent([
            'event' => 'test',
            'time' => 'test',
        ]);

        $this->assertEquals(count($this->logger->events), 1);
    }
}
