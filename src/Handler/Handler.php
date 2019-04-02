<?php

namespace PE\Component\Cronos\Logger\Handler;

use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Core\Queue;

/**
 * Base logger handler
 */
abstract class Handler implements HandlerInterface
{
    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onStarting(): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onStarted(): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onEnqueueTasks(Queue $queue): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onWaitingTasks(): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onTaskExecuted(TaskInterface $task): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onTaskEstimate(TaskInterface $task): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onTaskProgress(TaskInterface $task): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onTaskFinished(TaskInterface $task): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onStopping(): void
    {}

    /**
     * @inheritDoc
     * @codeCoverageIgnore
     */
    public function onStopped(): void
    {}

    /**
     * Format milliseconds to human readable format
     *
     * @param int $millis
     *
     * @return string
     */
    protected function formatDuration(int $millis): string
    {
        $seconds = $millis / 1000;
        $millis  %= 1000;

        $minutes = $seconds / 60;
        $seconds %= 60;

        $hours   = $minutes / 60;
        $minutes %= 60;

        return sprintf('%03dh %02dm %02ds %03dms', $hours, $minutes, $seconds, $millis);
    }
}