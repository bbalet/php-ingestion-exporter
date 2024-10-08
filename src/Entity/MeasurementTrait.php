<?php

namespace Bbalet\PhpIngestionExporter\Entity;

trait MeasurementTrait {
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
}
