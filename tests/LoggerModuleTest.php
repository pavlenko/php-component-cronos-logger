<?php

namespace PE\Component\Cronos\Logger\Tests;

use PE\Component\Cronos\Logger\Handler\HandlerInterface;
use PE\Component\Cronos\Logger\LoggerModule;
use PE\Component\Cronos\Core\ServerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoggerModuleTest extends TestCase
{
    /**
     * @var HandlerInterface|MockObject
     */
    private $handler;

    /**
     * @var LoggerModule
     */
    private $module;

    protected function setUp()
    {
        $this->handler = $this->createMock(HandlerInterface::class);
        $this->module  = new LoggerModule($this->handler);
    }

    public function testAttachServer(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $server->expects(static::exactly(10))->method('attachListener')->withConsecutive(
            [ServerInterface::EVENT_STARTING, [$this->handler, 'onStarting'], ServerInterface::EVENT_PRIORITY_HIGHEST],
            [ServerInterface::EVENT_STARTED, [$this->handler, 'onStarted'], ServerInterface::EVENT_PRIORITY_LOWEST],
            [ServerInterface::EVENT_ENQUEUE_TASKS, [$this->handler, 'onEnqueueTasks']],
            [ServerInterface::EVENT_WAITING_TASKS, [$this->handler, 'onWaitingTasks']],
            [ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->handler, 'onTaskExecuted']],
            [ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->handler, 'onTaskEstimate']],
            [ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->handler, 'onTaskProgress']],
            [ServerInterface::EVENT_SET_TASK_FINISHED, [$this->handler, 'onTaskFinished']],
            [ServerInterface::EVENT_STOPPING, [$this->handler, 'onStopping'], ServerInterface::EVENT_PRIORITY_HIGHEST],
            [ServerInterface::EVENT_STOPPED, [$this->handler, 'onStopped'], ServerInterface::EVENT_PRIORITY_LOWEST]
        );

        $this->module->attachServer($server);
    }

    public function testDetachServer(): void
    {
        /* @var $server ServerInterface|MockObject */
        $server = $this->createMock(ServerInterface::class);

        $server->expects(static::exactly(10))->method('detachListener')->withConsecutive(
            [ServerInterface::EVENT_STARTING, [$this->handler, 'onStarting']],
            [ServerInterface::EVENT_STARTED, [$this->handler, 'onStarted']],
            [ServerInterface::EVENT_ENQUEUE_TASKS, [$this->handler, 'onEnqueueTasks']],
            [ServerInterface::EVENT_WAITING_TASKS, [$this->handler, 'onWaitingTasks']],
            [ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->handler, 'onTaskExecuted']],
            [ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->handler, 'onTaskEstimate']],
            [ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->handler, 'onTaskProgress']],
            [ServerInterface::EVENT_SET_TASK_FINISHED, [$this->handler, 'onTaskFinished']],
            [ServerInterface::EVENT_STOPPING, [$this->handler, 'onStopping']],
            [ServerInterface::EVENT_STOPPED, [$this->handler, 'onStopped']]
        );

        $this->module->detachServer($server);
    }
}
