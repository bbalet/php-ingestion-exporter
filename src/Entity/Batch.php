<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * A Batch is used to measure an ingestion of data.
 * A Batch can be composed of one or more fragments.
 */
class Batch extends BatchType {
    use MeasurementTrait;

    /**
     * Collection of Fragments
     * @var \ArrayObject
     */
    private $fragments;

    /**
     * Instantiate a Batch object
     * @param mixed $name name of the batch (sanitized)
     * @param mixed $description description of the batch (derivated from name is null)
     * @param mixed $id Optional unique identifier of the batch
     */
    function __construct($name, $description = null, $id = null) {
        $this->fragments = new \ArrayObject();
        parent::__construct($name, $description, $id);
    }

    /**
     * Create a Batch entity from the database
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $startTime
     * @param float $endTime
     * @param int $statusCode
     * @return Batch
     */
    static function createFromDB($id, $name, $description, $startTime, $endTime, $statusCode) {
        $instance = new self($name, $description, $id);
        $instance->microStartTime = $startTime;
        $instance->microEndTime = $endTime;
        $instance->statusCode = $statusCode;
        return $instance;
    }

    /**
     * Return a fragment by its name or null
     * @param string $name name of the fragment
     * @return Fragment|null
     */
    public function getFragmentByName($name) {
        return $this->fragments[$name] ?? null;
    }

    /**
     * Return all fragments
     * @return \ArrayObject
     */
    public function getFragments() {
        return $this->fragments;
    }

    /**
     * Add a fragment to the batch and start its timer
     * @param string $name name of the fragment
     * @return Fragment the new fragment
     */
    public function startFragment($name, $description = null) {
        $fragment = new Fragment($this, $name, $description);
        $fragment->start();
        $this->fragments[$name] = $fragment;
        return $fragment;
    }

    /**
     * End the measurement of a batch and all its children fragments.
     * save the current time in microseconds
     * @param int $statusCode status code of the batch
     * @return void
     */
    public function stop($statusCode = self::SUCCESS) {
        $this->statusCode = $statusCode;
        $this->microEndTime = microtime(true);  //the batch
        foreach ($this->fragments as $fragment) {
            $fragment->stop();
        }
    }
}
