<?php

namespace Bbalet\PhpIngestionExporter\Exporter\Prometheus;

use Bbalet\PhpIngestionExporter\Exporter\IExportableItem;
use Bbalet\PhpIngestionExporter\Entity\Batch;

class BatchAsGauge implements IExportableItem {

    /**
     * Concrete batch object
     * @var Batch
     */
    private $batch;

    /**
     * Instantiate a BatchAsGauge object
     * @param Batch $batch the batch to export
     */
    public function __construct(Batch $batch) {
        $this->batch = $batch;
    }

    /**
     * Export the batch as a Prometheus gauge metric. Example of output:
     * HELP dummy_name_duration_seconds Test of gauge
     * TYPE dummy_name_duration_seconds gauge
     * dummy_name_duration_seconds 0.02010703086853
     * @return string the Prometheus metric as a string
     */
    public function export() {
        $output = 'HELP ' . $this->batch->getName() . '_duration_seconds ' . ucfirst($this->batch->getDescription()) . PHP_EOL;
        $output .= 'TYPE ' . $this->batch->getName() . '_duration_seconds gauge' . PHP_EOL;
        $output .= $this->batch->getName() . '_duration_seconds ' . strval($this->batch->getElaspedTime()) . PHP_EOL;
        return $output;
    }
}
