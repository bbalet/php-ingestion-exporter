<?php

namespace Bbalet\PhpIngestionExporter\Exception;

/**
 * This exception occurs when a Batch was not found in the database
 */
class BatchNotFoundException extends \Exception
{
    /**
     * Instanciate a BatchNotFound exception
     */
    public function __construct() {
        $message = 'Batch not found';
        parent::__construct($message);
    }
}
