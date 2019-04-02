<?php

namespace PE\Component\Cronos\Logger\Handler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use PE\Component\Cronos\Core\TaskInterface;
use PE\Component\Cronos\Core\Queue;

final class HandlerDBAL extends Handler
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @param Connection $connection
     * @param string     $tableName
     */
    public function __construct(Connection $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     * @codeCoverageIgnore
     */
    public function initialize(): void
    {
        $platform  = $this->connection->getDatabasePlatform();
        $schemaOld = $this->connection->getSchemaManager()->createSchema();
        $schemaNew = clone $schemaOld;

        if ($schemaNew->hasTable($this->tableName)) {
            $schemaNew->dropTable($this->tableName);
        }

        $table = $schemaNew->createTable($this->tableName);
        $table->addColumn('id', Type::STRING, ['length' => 255]);
        $table->addColumn('moduleID', Type::STRING, ['length' => 255]);
        $table->addColumn('status', Type::INTEGER, ['unsigned' => true]);
        $table->addColumn('error', Type::TEXT, ['notnull' => false, 'length' => 65535]);
        $table->addColumn('scheduledAt', Type::DATETIME, ['notnull' => false]);
        $table->addColumn('executedAt', Type::DATETIME, ['notnull' => false]);
        $table->addColumn('finishedAt', Type::DATETIME, ['notnull' => false]);
        $table->setPrimaryKey(['id', 'moduleID']);

        foreach ($schemaOld->getMigrateToSql($schemaNew, $platform) as $sql) {
            $this->connection->executeQuery($sql);
        }
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onEnqueueTasks(Queue $queue): void
    {
        foreach ($queue->contents() as $task) {
            $data = [
                'id'          => $task->getID(),
                'moduleID'    => $task->getModuleID(),
                'scheduledAt' => $task->getScheduledAt() ? $task->getScheduledAt()->format('Y-m-d H:i:s') : null,
            ];

            $this->connection->insert($this->tableName, $data);
        }
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onTaskExecuted(TaskInterface $task): void
    {
        $key = ['id' => $task->getID(), 'moduleID' => $task->getModuleID()];
        $val = [
            'executedAt' => $task->getExecutedAt() ? $task->getExecutedAt()->format('Y-m-d H:i:s') : null,
            'status'     => $task->getStatus(),
        ];

        $this->connection->update($this->tableName, $val, $key);
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onTaskEstimate(TaskInterface $task): void
    {
        $key = ['id' => $task->getID(), 'moduleID' => $task->getModuleID()];
        $val = ['estimate' => $task->getEstimate()];

        $this->connection->update($this->tableName, $val, $key);
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onTaskProgress(TaskInterface $task): void
    {
        $key = ['id' => $task->getID(), 'moduleID' => $task->getModuleID()];
        $val = ['progress' => $task->getProgress()];

        $this->connection->update($this->tableName, $val, $key);
    }

    /**
     * @inheritDoc
     * @throws \Doctrine\DBAL\DBALException
     */
    public function onTaskFinished(TaskInterface $task): void
    {
        $key = ['id' => $task->getID(), 'moduleID' => $task->getModuleID()];
        $val = [
            'finishedAt' => $task->getFinishedAt() ? $task->getFinishedAt()->format('Y-m-d H:i:s') : null,
            'status'     => $task->getStatus(),
            'error'      => $task->getError() ? (string) $task->getError() : null,
        ];

        $this->connection->update($this->tableName, $val, $key);
    }
}
