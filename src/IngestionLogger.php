<?php

namespace Bbalet\PhpIngestionExporter;

use Bbalet\PhpIngestionExporter\Entity\BatchCollection;

class IngestionLogger {

    /**
     * Collection of batches
     * @var \ArrayObject
     */
    private $batches;

    /**
     * PDO Object for the database
     * @var 
     */
    private $dbConnection;

    public function __construct() {
        $this->batches = new \ArrayObject();
        
    }

    public static function withPDOObject($connectionObject) {
        $instance = new self();
        
        return $instance;
    }

    public static function withConnectionString($connectionString) {
        $instance = new self();
        
        return $instance;
    }

}