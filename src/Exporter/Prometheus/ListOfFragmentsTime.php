<?php

namespace Bbalet\PhpIngestionExporter\Exporter\Prometheus;

use Bbalet\PhpIngestionExporter\Exporter\IExportableItem;
use Bbalet\PhpIngestionExporter\Entity\Batch;

class ListOfFragmentsTime implements IExportableItem {

    /**
     * Concrete batch object
     * @var Batch
     */
    private $batch;

    /**
     * Instantiate a ListOfFragmentsTime object
     * @param Batch $batch batch containing the fragments to export
     */
    public function __construct(Batch $batch) {
        $this->batch = $batch;
    }

    /**
     * Export the list of fragments execution times (parts of a batch) as a Prometheus gauge metric.
     * Example of output:
     *   \# HELP dummy_name_component List of fragments execution times for the batch dummy_name.
     *   \# TYPE dummy_name_component gauge
     *   dummy_name_component{component="file1",description="file 1"} 0.02010703086853
     *   dummy_name_component{component="file2",description="file 2"} 1.02010703086853
     * @return string the Prometheus metric as a string
     */
    public function export() {
        $output = '# HELP ' . $this->batch->getName() . '_component List of fragments execution times for the batch ' . $this->batch->getName() . PHP_EOL;
        $output .= '# TYPE ' . $this->batch->getName() . '_component gauge' . PHP_EOL;
        $fragments = $this->batch->getFragments();
        foreach ($fragments as $fragment) {
            $output .= $this->batch->getName() . '_component{component="' . $fragment->getName() . '",description="' . $fragment->getDescription() . '"} ' . strval($fragment->getElaspedTime()) . PHP_EOL;
        }
        return $output;
    }

}
