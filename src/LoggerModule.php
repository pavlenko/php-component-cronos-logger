<?php

namespace PE\Component\Cronos\Logger;

use PE\Component\Cronos\Core\Module;
use PE\Component\Cronos\Core\ServerInterface;
use PE\Component\Cronos\Logger\Handler\HandlerInterface;

class LoggerModule extends Module
{
    /**
     * @var HandlerInterface
     */
    private $handler;

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @inheritDoc
     */
    public function attachServer(ServerInterface $server): void
    {
        $server->attachListener(ServerInterface::EVENT_STARTING, [$this->handler, 'onStarting'], ServerInterface::EVENT_PRIORITY_HIGHEST);
        $server->attachListener(ServerInterface::EVENT_STARTED, [$this->handler, 'onStarted'], ServerInterface::EVENT_PRIORITY_LOWEST);
        $server->attachListener(ServerInterface::EVENT_ENQUEUE_TASKS, [$this->handler, 'onEnqueueTasks']);
        $server->attachListener(ServerInterface::EVENT_WAITING_TASKS, [$this->handler, 'onWaitingTasks']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->handler, 'onTaskExecuted']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->handler, 'onTaskEstimate']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->handler, 'onTaskProgress']);
        $server->attachListener(ServerInterface::EVENT_SET_TASK_FINISHED, [$this->handler, 'onTaskFinished']);
        $server->attachListener(ServerInterface::EVENT_STOPPING, [$this->handler, 'onStopping'], ServerInterface::EVENT_PRIORITY_HIGHEST);
        $server->attachListener(ServerInterface::EVENT_STOPPED, [$this->handler, 'onStopped'], ServerInterface::EVENT_PRIORITY_LOWEST);
    }

    /**
     * @inheritDoc
     */
    public function detachServer(ServerInterface $server): void
    {
        $server->detachListener(ServerInterface::EVENT_STARTING, [$this->handler, 'onStarting']);
        $server->detachListener(ServerInterface::EVENT_STARTED, [$this->handler, 'onStarted']);
        $server->detachListener(ServerInterface::EVENT_ENQUEUE_TASKS, [$this->handler, 'onEnqueueTasks']);
        $server->detachListener(ServerInterface::EVENT_WAITING_TASKS, [$this->handler, 'onWaitingTasks']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_EXECUTED, [$this->handler, 'onTaskExecuted']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_ESTIMATE, [$this->handler, 'onTaskEstimate']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_PROGRESS, [$this->handler, 'onTaskProgress']);
        $server->detachListener(ServerInterface::EVENT_SET_TASK_FINISHED, [$this->handler, 'onTaskFinished']);
        $server->detachListener(ServerInterface::EVENT_STOPPING, [$this->handler, 'onStopping']);
        $server->detachListener(ServerInterface::EVENT_STOPPED, [$this->handler, 'onStopped']);
    }
}
