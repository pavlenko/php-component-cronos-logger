<?php

namespace PE\Component\Cronos\Logger\Tests\Handler;

use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Logger\Handler\HandlerEcho;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class HandlerEchoTest extends TestCase
{
    public function testOnStarting(): void
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Starting server ...\n");
        (new HandlerEcho())->onStarting();
    }

    public function testOnStarted(): void
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Starting server OK\n");
        (new HandlerEcho())->onStarted();
    }

    public function testOnWaitingTasks(): void
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Waiting for tasks ...\n");
        (new HandlerEcho(true))->onWaitingTasks();
    }

    public function testOnTaskExecuted(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');

        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Execute task: Foo ...\n");
        (new HandlerEcho())->onTaskExecuted($task);
    }

    public function testOnTaskFinishedSuccess(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_DONE);

        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Execute task: Foo OK\n");
        (new HandlerEcho())->onTaskFinished($task);
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

        $date = date('Y-m-d H:i:s') . ': ';
        $this->expectOutputString("{$date}Execute task: Foo OK\n{$date}Execute task: Foo time: 000h 00m 01s 500ms\n");
        (new HandlerEcho(true))->onTaskFinished($task);
    }

    public function testOnTaskFinishedError(): void
    {
        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_ERROR);

        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Execute task: Foo ERROR\n");
        (new HandlerEcho())->onTaskFinished($task);
    }

    public function testOnTaskFinishedErrorAndDetails(): void
    {
        $exception = new \Exception();

        /* @var $task TaskInterface|MockObject */
        $task = $this->createMock(TaskInterface::class);
        $task->expects(static::once())->method('getName')->willReturn('Foo');
        $task->expects(static::once())->method('getStatus')->willReturn(TaskInterface::STATUS_ERROR);
        $task->expects(static::once())->method('getError')->willReturn($exception);

        $date = date('Y-m-d H:i:s') . ': ';
        $this->expectOutputString("{$date}Execute task: Foo ERROR\n{$date}{$exception}\n");
        (new HandlerEcho(true))->onTaskFinished($task);
    }

    public function testOnStopping(): void
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Stopping server ...\n");
        (new HandlerEcho())->onStopping();
    }

    public function testOnStopped(): void
    {
        $this->expectOutputString(date('Y-m-d H:i:s') . ': ' . "Stopping server OK\n");
        (new HandlerEcho())->onStopped();
    }
}
