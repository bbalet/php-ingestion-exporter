<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * A trait to measure the time of a process and its status
 */
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
     * Flag to know if the measurement has started
     * @var bool
     */
    private $isStarted = false;

    /**
     * Start a measurement, save the current time in microseconds
     * @return void
     */
    public function start() {
        $this->isStarted = true;
        $this->microStartTime = microtime(true);
    }

    /**
     * Return the start time attribute
     * @return float
     */
    public function getStartTime() {
        return $this->microStartTime;
    }

    /**
     * Return the end time attribute
     * @return float
     */
    public function getEndTime() {
        return $this->microEndTime;
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
     * Return the value of the isStarted attribute
     * @return bool
     */
    public function getIsStarted() {
        return $this->isStarted;
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
            "6 - Not Found";
    }
}
