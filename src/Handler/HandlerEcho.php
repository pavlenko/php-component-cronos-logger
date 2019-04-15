<?php

namespace PE\Component\Cronos\Logger\Handler;

use PE\Component\Cronos\Core\TaskInterface;

/**
 * Handler to log task execution to stdout
 */
class HandlerEcho extends Handler
{
    /**
     * @var bool
     */
    private $debug;

    /**
     * @param bool $debug
     */
    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    /**
     * @inheritDoc
     */
    public function onStarting(): void
    {
        echo "Starting server ...\n";
    }

    /**
     * @inheritDoc
     */
    public function onStarted(): void
    {
        echo "Starting server OK\n";
    }

    /**
     * @inheritDoc
     */
    public function onWaitingTasks(): void
    {
        if ($this->debug) {
            echo "Waiting for tasks ...\n";
        }
    }

    /**
     * @inheritDoc
     */
    public function onTaskExecuted(TaskInterface $task): void
    {
        echo sprintf("Execute task: %s ...\n", $task->getName());
    }

    /**
     * @inheritDoc
     */
    public function onTaskFinished(TaskInterface $task): void
    {
        if ($task->getStatus() === TaskInterface::STATUS_ERROR) {
            echo sprintf("Execute task: %s ERROR\n", $task->getName());

            if ($error = $task->getError()) {
                echo "{$error}\n";
            }
        } else {
            echo sprintf("Execute task: %s OK\n", $task->getName());
        }

        if ($this->debug && ($finishedAt = $task->getFinishedAt()) && ($executedAt = $task->getExecutedAt())) {
            echo sprintf(
                "Execute task: %s time: %s\n",
                $task->getName(),
                $this->formatDuration(($finishedAt->format('U.u') - $executedAt->format('U.u')) * 1000)
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function onStopping(): void
    {
        echo "Stopping server ...\n";
    }

    /**
     * @inheritDoc
     */
    public function onStopped(): void
    {
        echo "Stopping server OK\n";
    }
}
