<?php

namespace Bbalet\PhpIngestionExporter\Entity;

use Bbalet\PhpIngestionExporter\Exception\FragmentNotFound;

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
    function __construct($name, $description = "", $id = null) {
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
     * Add a fragment to the batch
     * @param Fragment $fragment
     * @return void
     */
    public function addFragment(Fragment $fragment) {
        $this->fragments[$fragment->getName()] = $fragment;
    }

    /**
     * Add a fragment to the batch and start its timer
     * @param string $name name of the fragment
     * @return void
     */
    public function startFragment($name, $description = null) {
        $fragment = new Fragment($name, $description);
        $fragment->start();
        $this->fragments[$name] = $fragment;
    }

    /**
     * Add a fragment to the batch and start its timer
     * @param string $name name of the fragment
     * @param int $statusCode status code of the fragment
     * @return void
     * @throws FragmentNotFound
     */
    public function stopFragment($name, $statusCode = Fragment::SUCCESS) {
        if (!is_null($this->fragments[$name])) {
            $this->fragments[$name]->stop($statusCode);
        } else {
            throw new FragmentNotFound();
        }
    }

    /**
     * Add a fragment to the batch and start its timer
     * Additional details are gathered from the file
     * @param string $name name of the fragment
     * @return void
     */
    public function startFragmentWithFileStats($filePath, $name, $description = "") {
        $fragment = Fragment::withFileStats($filePath, $name, $description);
        $fragment->start();
        $this->fragments[$name] = $fragment;
    }

    /**
     * End the measurement of a batch and all its children fragments.
     * save the current time in microseconds
     * Children fragments are stopped with the status code UNKNOWN
     * @param int $statusCode status code of the batch
     * @return void
     */
    public function stop($statusCode = self::SUCCESS) {
        $this->statusCode = $statusCode;
        $this->isStarted = false;
        foreach ($this->fragments as $fragment) {
            if ($fragment->getIsStarted()) {
                $fragment->stop(Fragment::UNKNOWN);
            }
        }
        $this->microEndTime = microtime(true);
    }

    /**
     * End the measurement of a fragment, save the current time in microseconds
     *
     * @param string $name Name of the fragment
     * @param int $fileSize Size of the file in bytes
     * @param int $linesCount Number of lines in the file
     * @param int $statusCode Status code of the fragment
     * @return void
     * @throws FragmentNotFound
     */
    public function stopFragmentWithFileInfos($name, $fileSize, $linesCount, $statusCode = Fragment::SUCCESS) {
        if (!is_null($this->fragments[$name])) {
            $this->fragments[$name]->stop($statusCode, $fileSize, $linesCount);
        } else {
            throw new FragmentNotFound();
        }
    }
}
