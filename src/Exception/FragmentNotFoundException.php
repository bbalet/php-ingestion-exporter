<?php

namespace Bbalet\PhpIngestionExporter\Exception;

/**
 * This exception occurs when a fragment was not found in the batch
 */
class FragmentNotFoundException extends \Exception
{
    /**
     * Instanciate a FragmentNotFound exception
     */
    public function __construct() {
        $message = 'Fragment not found in the batch';
        parent::__construct($message);
    }
}
