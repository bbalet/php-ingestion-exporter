<?php

namespace Bbalet\PhpIngestionExporter\Database;

use Bbalet\PhpIngestionExporter\Entity\BatchType;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Entity\Fragment;

/**
 * MySQLDatabase database implementation
 * This class is responsible for managing the MySQLDatabase database
 * it creates the schema and provides methods to interact with it
 * TODO : test this class
 */
class MySQLDatabase extends AbstractDatabase
{
    /**
     * Construct a repository with a PDO connection to the MySQLDatabase database
      * @param \PDO $pdoConnection
     * @param bool $migrate whether to migrate the schema or not
     * @param string $prefix prefix for the tables
     */
    public function __construct($pdoConnection, $migrate=true, $prefix = 'pingexp_')
    {
        $this->pdoConnection = $pdoConnection;
        $this->prefix = $prefix;
        if ($migrate) {
            $this->migrateSchema();
        }
    }

    /**
     * Get the parameters from the database
     * @return array<string, string>
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
     * @return void
     */
    public function setParameter($key, $value) {
        $stmt = $this->pdoConnection->prepare("INSERT OR REPLACE INTO {$this->prefix}_parameter (key, value) VALUES (:key, :value);");
        $stmt->execute(array(':key' => $key, ':value' => $value));
    }

    /**
     * Persist a BatchType entity into the database
     * @param BatchType $batchType
     * @return BatchType with the ID from database
     */
    public function setBatchType($batchType) {
        $stmt = $this->pdoConnection->prepare("INSERT OR REPLACE INTO {$this->prefix}_batch_type (name, description) VALUES (:name, :description);");
        $stmt->execute(array(':name' => $batchType->getName(), ':description' => $batchType->getDescription()));
        return $this->getBatchType($batchType->getName());
    }

    /**
     * Get a BatchType entity from the database
     * @param string $name
     * @return BatchType
     */
    public function getBatchType($name) {
        $stmt = $this->pdoConnection->query("SELECT id, name, description FROM {$this->prefix}_batch_type WHERE name=:name;");
        $stmt->execute(array(':name' => $name));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new BatchType($row['name'], $row['description'], $row['id']);
    }

    /**
     * Persist a Batch entity into the database
     * @param Batch $batch
     * @return void
     */
    public function setBatch($batch) {
        $batchType = new BatchType($batch->getName(), $batch->getDescription());
        $batchType = $this->setBatchType($batchType);
        $stmt = $this->pdoConnection->prepare("INSERT INTO {$this->prefix}_batch 
            (batch_type_id, start_time, end_time, status_code) 
            VALUES (:batch_type_id, :start_time, :end_time, :status_code);");
        $stmt->execute(array(
            ':batch_type_id' => $batchType->getId(),
            ':start_time' => $batch->getStartTime(),
            ':end_time' => $batch->getEndTime(),
            ':status_code' => $batch->getStatusCode()
            ));

        //Get the last inserted row id
        $stmt = $this->pdoConnection->query("SELECT last_insert_rowid() AS id;");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $batch->setId($row['id']);

        //Insert the collection of fragments
        $fragments = $batch->getFragments();
        if (count($fragments) > 0) {
            $data = [];
            foreach ($fragments as $fragment) {
                $data[] = [
                    $batch->getId(),
                    $fragment->getName(),
                    $fragment->getDescription(),
                    strval($fragment->getStartTime()),
                    strval($fragment->getEndTime()),
                    $fragment->getStatusCode(),
                    $fragment->getLinesCount(),
                    $fragment->getFileSize()
                ];
            }

            $values = str_repeat('?,', count($data[0]) - 1) . '?';
            $sql = "INSERT INTO {$this->prefix}_fragment (batch_id, name, description, start_time, end_time, status_code, lines, filesize) VALUES " .
                    str_repeat("($values),", count($data) - 1) . "($values)";    
            $stmt = $this->pdoConnection->prepare($sql);
            $stmt->execute(array_merge(...$data));
        }
    }

    /**
     * Get the last executed Batch entity from the database
     * @param string $name
     * @return Batch|null the batch or null if not found
     */
    public function getLastBatch($name) {
        //Get the last batch of the given batch type
        $stmt = $this->pdoConnection->query("SELECT * FROM {$this->prefix}_batch, {$this->prefix}_batch_type
            WHERE batch_type_id = {$this->prefix}_batch_type.id
            AND name=:name
            ORDER BY date(start_time) ASC LIMIT 1;");
        $stmt->execute(array(':name' => $name));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row !== false) {
            $batch = Batch::createFromDB(
                $row['id'],
                $row['name'],
                $row['description'],
                floatval($row['start_time']),
                floatval($row['end_time']),
                $row['status_code']);

            //Load the list of fragments that are part of the batch
            $stmt = $this->pdoConnection->query("SELECT * FROM {$this->prefix}_fragment WHERE batch_id=:batch_id");
            $stmt->execute(array(':batch_id' => $batch->getId()));
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $fragment = Fragment::createFromDB(
                    $row['id'],
                    $row['name'],
                    $row['description'],
                    floatval($row['start_time']),
                    floatval($row['end_time']),
                    $row['status_code'],
                    $row['filesize'],
                    $row['lines']);
                $batch->addFragment($fragment);
            }
            return $batch;
        } else {
            return null;
        }
    }

    /**
     * Migrate the schema of the 
     * @return void
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
            id INTEGER PRIMARY KEY,
            name TEXT,
            description TEXT
        );";
        $this->pdoConnection->exec($createBatchTypeTable);
        
        $createIndexOnBatchTypeTable = "CREATE UNIQUE INDEX {$this->prefix}_batch_type_name
            ON {$this->prefix}_batch_type(name);";
        $this->pdoConnection->exec($createIndexOnBatchTypeTable);

        // Create the batch table
        $createBatchTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_batch (
            id INTEGER PRIMARY KEY,
            batch_type_id INTEGER,
            start_time REAL,
            end_time REAL,
            status_code INTEGER,
            CONSTRAINT {$this->prefix}_batch_batch_type_id
            FOREIGN KEY (batch_type_id) REFERENCES {$this->prefix}_batch_type(id) 
        );";
        $this->pdoConnection->exec($createBatchTable);

        // Create the fragment table
        $createFragmentTable = "CREATE TABLE IF NOT EXISTS {$this->prefix}_fragment (
            id INTEGER PRIMARY KEY,
            batch_id INTEGER,
            name TEXT,
            description TEXT,
            start_time REAL,
            end_time REAL,
            status_code INTEGER,
            lines INTEGER,
            filesize INTEGER,
            CONSTRAINT {$this->prefix}_fragment_batch_id
            FOREIGN KEY (batch_id) REFERENCES {$this->prefix}_batch(id) 
        );";
        $this->pdoConnection->exec($createFragmentTable);
    }
}
