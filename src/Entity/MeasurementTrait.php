<?php

namespace Bbalet\PhpIngestionExporter\Entity;

trait MeasurementTrait {
    
    const SUCCESS = 0;
    const FAILURE = 1;
    const WARNING = 2;
    const UNKNOWN = 3;
    const PARTIAL = 4;
    const TIMEOUT = 5;
    const NOT_FOUND = 6;
    
    /**
     * Start time in microseconds
     * @var float
     */
    private $microStartTime;

    /**
     * End time in microseconds  
     * @var float
     */
    private $microEndTime;

    /**
     * Status code of the measurement
     * @var int
     */
    private $statusCode;

    /**
     * Start a measurement, save the current time in microseconds
     * @return void
     */
    public function start() {
        $this->microStartTime = microtime(true);
    }

    /**
     * Return the duration of a measurement in microseconds
     * @return float elapsed time between start and end
     */
    public function getElaspedTime() {
        return $this->microEndTime - $this->microStartTime;
    }

    /**
     * Return the status code of the measurement
     * @return int status code
     */
    public function getStatusCode() {
        return $this->statusCode;
    }

    /**
     * Return a string containing a list of status codes and their descriptions
     *
     * @return string list of status codes explanations
     */
    public function describeListOfStatusCodes() {
        return "0 - Success, " .
            "1 - Failure, " .
            "2 - Warning, " .
            "3 - Unknown, " .
            "4 - Partial, " .
            "5 - Timeout, " .
            "6 - Not Found ";
    }
}
