<?php

use Bbalet\PhpIngestionExporter\Entity\Batch;

class BatchAsGauge implements IExportableItem {

    /**
     * Concrete batch object
     * @var Batch
     */
    private $batch;

    public function __construct(Batch $batch) {
        $this->batch = $batch;
    }

    public function toExportableFormat() {
        return strval($this->batch->getElaspedTime());
    }

}