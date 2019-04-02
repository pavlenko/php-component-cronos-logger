<?php

namespace PE\Component\Cronos\Logger\Handler;

use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Core\Queue;

interface HandlerInterface
{
    /**
     * Triggers when server starting begin
     */
    public function onStarting(): void;

    /**
     * Triggers when server starting complete
     */
    public function onStarted(): void;

    /**
     * Triggers when tasks added to queue
     *
     * @param Queue $queue
     */
    public function onEnqueueTasks(Queue $queue): void;

    /**
     * Triggers when no tasks to execute now
     */
    public function onWaitingTasks(): void;

    /**
     * Triggers when task execution started
     *
     * @param TaskInterface $task
     */
    public function onTaskExecuted(TaskInterface $task): void;

    /**
     * Triggers when task estimate updated
     *
     * @param TaskInterface $task
     */
    public function onTaskEstimate(TaskInterface $task): void;

    /**
     * Triggers when task progress updated
     *
     * @param TaskInterface $task
     */
    public function onTaskProgress(TaskInterface $task): void;

    /**
     * Triggers when task execution complete
     *
     * @param TaskInterface $task
     */
    public function onTaskFinished(TaskInterface $task): void;

    /**
     * Triggers when server stopping begin
     */
    public function onStopping(): void;

    /**
     * Triggers when server stopping complete
     */
    public function onStopped(): void;
}