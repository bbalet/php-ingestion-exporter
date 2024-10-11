<?php

namespace Bbalet\PhpIngestionExporter\Database;

use Bbalet\PhpIngestionExporter\Entity\BatchType;

/**
 * SQLite database implementation
 * This class is responsible for managing the SQLite database
 * it creates the schema and provides methods to interact with it
 */
class SqliteDatabase extends AbstractDatabase
{
    /**
     * Construct a repository with a PDO connection to the SQLite database
     * @param \PDO $pdoConnection
     */
    public function __construct($pdoConnection, $prefix = 'pingexp_')
    {
        $this->pdoConnection = $pdoConnection;
        $this->prefix = $prefix;
        $this->migrateSchema();
    }

    /**
     * Get the parameters from the database
     * @return array
     */
    public function getParameters() {
        $stmt = $this->pdoConnection->query("SELECT key, value FROM {$this->prefix}_parameter;");
        $params  = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        return $params;
    }

    /**
     * Set a parameter in the database
     * @param string $key
     * @param string $value
     */
    public function setParameter($key, $value) {
        $stmt = $this->pdoConnection->prepare("INSERT OR REPLACE INTO {$this->prefix}_parameter (key, value) VALUES (:key, :value);");
        $stmt->execute(array(':key' => $key, ':value' => $value));
    }

    /**
     * Persist a BatchType entity into the database
     * @param BatchType $batchType
     * @return void
     */
    public function setBatchType($batchType) {
        $stmt = $this->pdoConnection->prepare("INSERT OR REPLACE INTO {$this->prefix}_batch_type (name, description) VALUES (:name, :description);");
        $stmt->execute(array(':name' => $batchType->getName(), ':description' => $batchType->getDescription()));
    }

    /**
     * Get a BatchType entity from the database
     * @param string $name
     * @return BatchType
     */
    public function getBatchType($name) {
        $stmt = $this->pdoConnection->query("SELECT name, description FROM {$this->prefix}_batch_type WHERE name=:name;");
        $stmt->execute(array(':name' => $name));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new BatchType($row['name'], $row['description']);
    }

    /**
     * Migrate the schema of the database
     */
    protected function migrateSchema() {
        // Create the parameter table
        $createParameterTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_parameter (
            key TEXT PRIMARY KEY,
            value TEXT
        );";
        $this->pdoConnection->exec($createParameterTable);
        // Create the batch type table
        $createBatchTypeTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_batch_type (
            batch_type_id INTEGER PRIMARY KEY,
            name TEXT,
            description TEXT
        );";
        $this->pdoConnection->exec($createBatchTypeTable);
        
        $createIndexOnBatchTypeTable = "CREATE UNIQUE INDEX {$this->prefix}_batch_type_name
            ON {$this->prefix}_batch_type(name);";
        $this->pdoConnection->exec($createIndexOnBatchTypeTable);

        // Create the batch table
        $createBatchTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_batch (
            batch_id INTEGER PRIMARY KEY,
            batch_type_id INTEGER,
            start_time REAL,
            end_time REAL,
            status_code INTEGER,
            CONSTRAINT {$this->prefix}_batch_batch_type_id
            FOREIGN KEY (batch_type_id) REFERENCES {$this->prefix}_batch_type(batch_type_id) 
        );";
        $this->pdoConnection->exec($createBatchTable);
    }
}


