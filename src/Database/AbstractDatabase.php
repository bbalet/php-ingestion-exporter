<?php

namespace Bbalet\PhpIngestionExporter\Database;

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
     * Migrate the schema of the database
     */
    abstract protected function migrateSchema();    
}


