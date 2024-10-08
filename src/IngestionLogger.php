<?php

namespace Bbalet\PhpIngestionExporter;

use Bbalet\PhpIngestionExporter\Entity\BatchCollection;

class IngestionLogger {

    /**
     * Collection of batches
     * @var BatchCollection
     */
    private $batches;

    /**
     * PDO Object for the database
     * @var 
     */
    private $dbConnection;

    public function __construct() {
        
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