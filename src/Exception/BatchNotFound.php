<?php

namespace Bbalet\PhpIngestionExporter\Exception;

/**
 * This exception occurs when a Batch was not found in the database
 */
class BatchNotFound extends \Exception
{
    /**
     * Instanciate a FragmentNotFound exception
     */
    public function __construct() {
        $message = 'Batch not found in the database';
        parent::__construct($message);
    }
}
