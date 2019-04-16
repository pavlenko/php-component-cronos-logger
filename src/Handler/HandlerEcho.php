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
        $this->log('Starting server ...');
    }

    /**
     * @inheritDoc
     */
    public function onStarted(): void
    {
        $this->log('Starting server OK');
    }

    /**
     * @inheritDoc
     */
    public function onWaitingTasks(): void
    {
        if ($this->debug) {
            $this->log("Waiting for tasks ...");
        }
    }

    /**
     * @inheritDoc
     */
    public function onTaskExecuted(TaskInterface $task): void
    {
        $this->log(sprintf('Execute task: %s ...', $task->getName()));
    }

    /**
     * @inheritDoc
     */
    public function onTaskFinished(TaskInterface $task): void
    {
        if ($task->getStatus() === TaskInterface::STATUS_ERROR) {
            $this->log(sprintf('Execute task: %s ERROR', $task->getName()));

            if ($error = $task->getError()) {
                if ($this->debug) {
                    $this->log((string) $error);
                } else {
                    $this->log($error->getMessage());
                }
            }
        } else {
            $this->log( sprintf('Execute task: %s OK', $task->getName()));
        }

        if ($this->debug && ($finishedAt = $task->getFinishedAt()) && ($executedAt = $task->getExecutedAt())) {
            $this->log(sprintf(
                'Execute task: %s time: %s',
                $task->getName(),
                $this->formatDuration(($finishedAt->format('U.u') - $executedAt->format('U.u')) * 1000)
            ));
        }
    }

    /**
     * @inheritDoc
     */
    public function onStopping(): void
    {
        $this->log('Stopping server ...');
    }

    /**
     * @inheritDoc
     */
    public function onStopped(): void
    {
        $this->log('Stopping server OK');
    }

    /**
     * @param string $message
     */
    private function log(string $message): void
    {
        echo date('Y-m-d H:i:s') . ': ' . $message . "\n";
    }
}
