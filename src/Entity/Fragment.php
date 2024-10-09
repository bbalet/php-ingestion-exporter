<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * A fragment is part of a batch that is used to measure its subparts
 */
class Fragment {
    use EntityTrait;
    use MeasurementTrait;

    /**
     * The batch to which the fragment belong to
     * @var Batch
     */
    private $parentBatch;

    /**
     * Instanciate a Fragment
     * @param \Bbalet\PhpIngestionExporter\Entity\Batch $parentBatch a fragment belongs to a batch
     * @param string $name Fragment name (this is sanitized)
     * @param string $description Fragment description, if null derivated from name
     */
    function __construct(Batch $parentBatch, $name, $description = null) {
        $this->parentBatch = $parentBatch;
        $this->statusCode = self::UNKNOWN;
        $this->setName($name);
        $this->setDescription($description);
    }

    /**
     * Return the parent batch
     * @return Batch
     */
    function getBatch() {
        return $this->parentBatch;
    }

    /**
     * End the measurement of a fragment, save the current time in microseconds
     * @param int $statusCode status code of the fragment
     * @return void
     */
    public function stop($statusCode = self::SUCCESS) {
        $this->statusCode = $statusCode;
        $this->microEndTime = microtime(true);
    }
}
