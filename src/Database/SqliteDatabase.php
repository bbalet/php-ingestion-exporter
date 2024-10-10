<?php

namespace Bbalet\PhpIngestionExporter\Database;

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
        $stmt = $this->pdoConnection->query("SELECT key, value FROM {$this->prefix}_param;");
        $params  = $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
        return $params;
    }

    /**
     * Set a parameter in the database
     * @param string $key
     * @param string $value
     */
    public function setParameter($key, $value) {
        $stmt = $this->pdoConnection->prepare("INSERT OR REPLACE INTO {$this->prefix}_param (key, value) VALUES (:key, :value);");
        $stmt->execute(array(':key' => $key, ':value' => $value));
    }

    /**
     * Migrate the schema of the database
     */
    protected function migrateSchema() {
        // Create the parameter table
        $createParamTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_param (
            key TEXT PRIMARY KEY,
            value TEXT
        );";
        $this->pdoConnection->exec($createParamTable);
    }
}


