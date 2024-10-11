<?php

namespace Bbalet\PhpIngestionExporter\Database;

use Bbalet\PhpIngestionExporter\Entity\BatchType;
use Bbalet\PhpIngestionExporter\Entity\Batch;

/**
 * Abtraction of a database
 */
abstract class AbstractDatabase
{
    /**
     * PDO connection
     * @var \PDO
     */
    protected $pdoConnection;

    /**
     * Table prefix
     * @var string
     */
    protected $prefix;

    /**
     * Set a parameter in the database
     * @param string $key
     * @param string $value
     */
    abstract public function setParameter($key, $value);

    /**
     * Get the parameters from the database
     * @return array
     */
    abstract public function getParameters();


    /**
     * Persist a BatchType entity into the database
     * @param BatchType $batchType
     * @return void
     */
    abstract public function setBatchType($batchType);

    /**
     * Get a BatchType entity from the database
     * @param mixed $name
     * @return BatchType
     */
    abstract public function getBatchType($name);

    /**
     * Persist a Batch entity into the database
     * @param Batch $batch
     * @return void
     */
    abstract public function setBatch($batch);

    /**
     * Get the last executed Batch entity from the database
     * @param string $name
     * @return Batch
     */
    abstract public function getLastBatch($name);

    /**
     * Migrate the schema of the database
     */
    abstract protected function migrateSchema();    
}
