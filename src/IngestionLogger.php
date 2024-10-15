<?php

namespace Bbalet\PhpIngestionExporter;

use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Database\AbstractDatabase;
use Bbalet\PhpIngestionExporter\Database\DatabaseFactory;
use Bbalet\PhpIngestionExporter\Exception\BatchNotFoundException;
use Bbalet\PhpIngestionExporter\Exporter\PrometheusExporter;

/**
 * IngestionLogger is a class that allows to log the ingestion of data in a database.
 */ 
class IngestionLogger {

    /**
     * Collection of batches
     * @var \ArrayObject<string, Batch>
     */
    private $batches;

    /**
     * PDO Object for the database
     * @var AbstractDatabase
     */
    private $db;
    
    /**
     * Prometheus of exporter
     * @var PrometheusExporter
     */
    private $prometheusExporter;

    /**
     * Instantiate a IngestionLogger object
     */
    public function __construct() {
        $this->batches = new \ArrayObject();
    }

    /**
     * Create a IngestionLogger object from a PDO connection
     * @param \PDO $connectionObject
     * @param bool $migrate whether to migrate the schema or not
     * @param string $prefix table name prefix
     * @return IngestionLogger
     */
    public static function withPDOObject($connectionObject, $migrate = true, $prefix = 'pingexp') {
        $instance = new self();
        $instance->db = DatabaseFactory::getDatabaseFromPDOObject($connectionObject);
        return $instance;
    }

    /**
     * Create a IngestionLogger object from a connection string
     * @param string $connectionString
     * @param bool $migrate whether to migrate the schema or not
     * @param string $prefix table name prefix
     * @return IngestionLogger
     */
    public static function withConnectionString($connectionString, $migrate = true, $prefix = 'pingexp') {
        $instance = new self();
        $instance->db = DatabaseFactory::getDatabaseFromConnectionString($connectionString);
        return $instance;
    }

    /**
     * Add a batch to the collection and start it
     * @param string $name name of the batch
     * @param string $description description of the batch
     * @return Batch
     */
    public function startBatch($name, $description = "") {
        $batch = new Batch($name, $description);
        $batch->start();
        $this->batches->append($batch);
        return $batch;
    }

    /**
     * Stop a batch from its name
     * @param string $name name of the fragment
     * @return void
     * @throws BatchNotFoundException
     */
    public function stopBatch($name) {
        if ($this->batches->offsetExists($name)) {
            $batch = $this->batches->offsetGet($name);
            $batch->stop();
            $this->db->setBatch($batch);
        } else {
            throw new BatchNotFoundException();
        }
    }

    /**
     * Get a batch from its name
     * @param string $name name of the fragment
     * @return Batch
     * @throws BatchNotFoundException
     */
    public function getBatch($name) {
        if ($this->batches->offsetExists($name)) {
            return $this->batches->offsetGet($name);
        } else {
            throw new BatchNotFoundException();
        }
    }

    /**
     * Instanciate a PrometheusExporter and return the instance
     * @return PrometheusExporter
     */
    public function getPrometheusExporter() {
        if ($this->prometheusExporter === null) {
            $this->prometheusExporter = new PrometheusExporter($this->db);
        }
        return $this->prometheusExporter;
    }


}