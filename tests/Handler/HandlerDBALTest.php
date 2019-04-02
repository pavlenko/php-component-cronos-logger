<?php

namespace PE\Component\Cronos\Logger\Tests\Handler;

use Doctrine\DBAL\Connection;
use PE\Component\Cronos\Core\Task;
use PE\Component\Cronos\Logger\Handler\HandlerDBAL;
use PE\Component\Cronos\Core\Queue;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerDBALTest extends TestCase
{
    const TABLE = 'logger';

    /**
     * @var Connection|MockObject
     */
    private $connection;

    /**
     * @var HandlerDBAL
     */
    private $handler;

    protected function setUp()
    {
        $this->connection = $this->createMock(Connection::class);
        $this->handler    = new HandlerDBAL($this->connection, self::TABLE);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testOnEnqueueTasks(): void
    {
        $date = date_create();
        $task = (new Task())->setID('TASK')->setModuleID('MODULE')->setScheduledAt($date);

        $queue = new Queue();
        $queue->enqueue($task);

        $val = [
            'id'          => 'TASK',
            'moduleID'    => 'MODULE',
            'scheduledAt' => $date->format('Y-m-d H:i:s'),
        ];

        $this->connection->expects(static::once())->method('insert')->with(self::TABLE, $val);

        $this->handler->onEnqueueTasks($queue);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testOnTaskExecuted(): void
    {
        $date = date_create();
        $task = (new Task())
            ->setID('TASK')
            ->setModuleID('MODULE')
            ->setStatus(Task::STATUS_IN_PROGRESS)
            ->setExecutedAt($date);

        $key = ['id' => 'TASK', 'moduleID' => 'MODULE'];
        $val = [
            'executedAt' => $date->format('Y-m-d H:i:s'),
            'status'     => Task::STATUS_IN_PROGRESS,
        ];

        $this->connection->expects(static::once())->method('update')->with(self::TABLE, $val, $key);

        $this->handler->onTaskExecuted($task);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testOnTaskEstimate(): void
    {
        $task = (new Task())
            ->setID('TASK')
            ->setModuleID('MODULE')
            ->setEstimate(1000);

        $key = ['id' => 'TASK', 'moduleID' => 'MODULE'];
        $val = ['estimate' => 1000];

        $this->connection->expects(static::once())->method('update')->with(self::TABLE, $val, $key);

        $this->handler->onTaskEstimate($task);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testOnTaskProgress(): void
    {
        $task = (new Task())
            ->setID('TASK')
            ->setModuleID('MODULE')
            ->setProgress(1000);

        $key = ['id' => 'TASK', 'moduleID' => 'MODULE'];
        $val = ['progress' => 1000];

        $this->connection->expects(static::once())->method('update')->with(self::TABLE, $val, $key);

        $this->handler->onTaskProgress($task);
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function testOnTaskFinished(): void
    {
        $date = date_create();
        $task = (new Task())
            ->setID('TASK')
            ->setModuleID('MODULE')
            ->setFinishedAt($date)
            ->setStatus(Task::STATUS_ERROR)
            ->setError($error = new \Exception('AAA'));

        $key = ['id' => 'TASK', 'moduleID' => 'MODULE'];
        $val = [
            'finishedAt' => $date->format('Y-m-d H:i:s'),
            'status'     => Task::STATUS_ERROR,
            'error'      => (string) $error,
        ];

        $this->connection->expects(static::once())->method('update')->with(self::TABLE, $val, $key);

        $this->handler->onTaskFinished($task);
    }
}
