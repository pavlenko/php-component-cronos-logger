<?php

namespace PE\Component\Cronos\Logger\Tests\Handler;

use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Logger\Handler\HandlerPSR;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class HandlerPSRTest extends TestCase
{
    /**
     * @var LoggerInterface|MockObject
     */
    private $logger;

    /**
     * @var HandlerPSR
     */
    private $handler;

    protected function setUp()
    {
        $this->logger  = $this->createMock(LoggerInterface::class);
        $this->handler = new HandlerPSR($this->logger);
    }

    public function testOnStarting(): void
    {
        $this->logger->expects(static::once())->method('info')->with('Starting server ...');
        $this->handler->onStarting();
    }

    public function testOnStarted(): void
    {
        $this->logger->expects(static::once())->method('info')->with('Starting server OK');
        $this->handler->onStarted();
    }

    public function testOnWaitingTasks(): void
    {
        $this->logger->expects(static::once())->method('debug')->with('Waiting for tasks ...');
        $this->handler->onWaitingTasks();
    }

    public function testOnTaskExecuted(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');

        $this->logger->expects(static::once())->method('info')->with('Execute task: Foo ...');
        $this->handler->onTaskExecuted($task);
    }

    public function testOnTaskFinishedSuccess(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_DONE);

        $this->logger->expects(static::once())->method('info')->with('Execute task: Foo OK');
        $this->handler->onTaskFinished($task);
    }

    public function testOnTaskFinishedSuccessAndDetails(): void
    {
        $time  = microtime(true);
        $date1 = \DateTime::createFromFormat('U.u', $time);
        $date2 = \DateTime::createFromFormat('U.u', $time + 1.5);

        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::exactly(2))->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_DONE);
        $task->expects(static::once())->method('getExecutedAt')->willReturn($date1);
        $task->expects(static::once())->method('getFinishedAt')->willReturn($date2);

        $this->logger->expects(static::once())->method('info')->with('Execute task: Foo OK');
        $this->logger->expects(static::once())->method('debug')->with("Execute task: Foo time: 000h 00m 01s 500ms\n");
        $this->handler->onTaskFinished($task);
    }

    public function testOnTaskFinishedError(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_ERROR);

        $this->logger->expects(static::once())->method('error')->with('Execute task: Foo ERROR');
        $this->handler->onTaskFinished($task);
    }

    public function testOnTaskFinishedErrorAndDetails(): void
    {
        $exception = new \Exception();

        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_ERROR);
        $task->expects(static::once())->method('getError')->willReturn($exception);

        $this->logger->expects(static::once())->method('error')->with('Execute task: Foo ERROR');
        $this->logger->expects(static::once())->method('debug')->with($exception);
        $this->handler->onTaskFinished($task);
    }

    public function testOnStopping(): void
    {
        $this->logger->expects(static::once())->method('info')->with('Stopping server ...');
        $this->handler->onStopping();
    }

    public function testOnStopped(): void
    {
        $this->logger->expects(static::once())->method('info')->with('Stopping server OK');
        $this->handler->onStopped();
    }
}
