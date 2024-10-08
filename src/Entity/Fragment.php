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
     * @var 
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
        $this->setName($name);
        $this->setDescription($description);
    }

    /**
     * End the measurement of a fragment, save the current time in microseconds
     * @return void
     */
    public function stop() {
        $this->microEndTime = microtime(true);
    }
}
