<?php

namespace PE\Component\Cronos\Logger\Handler;

use PE\Component\Cronos\Core\TaskInterface;
use Psr\Log\LoggerInterface;

/**
 * Handler to log task execution via PSR logger
 */
class HandlerPSR extends Handler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function onStarting(): void
    {
        $this->logger->info('Starting server ...');
    }

    /**
     * @inheritDoc
     */
    public function onStarted(): void
    {
        $this->logger->info('Starting server OK');
    }

    /**
     * @inheritDoc
     */
    public function onWaitingTasks(): void
    {
        $this->logger->debug('Waiting for tasks ...');
    }

    /**
     * @inheritDoc
     */
    public function onTaskExecuted(TaskInterface $task): void
    {
        $this->logger->info(sprintf('Execute task: %s ...', $task->getName()));
    }

    /**
     * @inheritDoc
     */
    public function onTaskFinished(TaskInterface $task): void
    {
        if ($task->getStatus() === TaskInterface::STATUS_ERROR) {
            $this->logger->error(sprintf('Execute task: %s ERROR', $task->getName()));

            if ($error = $task->getError()) {
                $this->logger->debug((string) $error);
            }
        } else {
            $this->logger->info(sprintf('Execute task: %s OK', $task->getName()));
        }

        if (($finishedAt = $task->getFinishedAt()) && ($executedAt = $task->getExecutedAt())) {
            $this->logger->debug(sprintf(
                "Execute task: %s time: %s\n",
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
        $this->logger->info('Stopping server ...');
    }

    /**
     * @inheritDoc
     */
    public function onStopped(): void
    {
        $this->logger->info('Stopping server OK');
    }
}