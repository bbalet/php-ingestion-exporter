<?php

namespace Bbalet\PhpIngestionExporter\Database;

use Bbalet\PhpIngestionExporter\Entity\BatchType;

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
     * Migrate the schema of the database
     */
    abstract protected function migrateSchema();    
}
