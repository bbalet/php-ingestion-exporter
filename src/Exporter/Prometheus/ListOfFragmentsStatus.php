<?php

namespace Bbalet\PhpIngestionExporter\Exporter\Prometheus;

use Bbalet\PhpIngestionExporter\Exporter\IExportableItem;
use Bbalet\PhpIngestionExporter\Entity\Batch;

class ListOfFragmentsStatus implements IExportableItem {

    /**
     * Concrete batch object
     * @var Batch
     */
    private $batch;

    /**
     * Instantiate a ListOfFragmentsStatus object
     * @param Batch $batch batch containing the fragments to export
     */
    public function __construct(Batch $batch) {
        $this->batch = $batch;
    }

    /**
     * Export the list of fragments status (parts of a batch) as a Prometheus gauge metric.
     * Example of output:
     *   \# HELP dummy_name_component List of fragments for the batch dummy_name. 0 - Success, 1 - Failure, 2 - Warning, 3 - Unknown, 4 - Partial, 5 - Timeout, 6 - Not Found
     *   \# TYPE dummy_name_component gauge
     *   dummy_name_component{component="file1",description="file 1"} 0
     *   dummy_name_component{component="file2",description="file 2"} 1
     * @return string the Prometheus metric as a string
     */
    public function export() {
        $output = '# HELP ' . $this->batch->getName() . '_component List of fragments status for the batch ' . $this->batch->getName()
         . '. 0 - Success, 1 - Failure, 2 - Warning, 3 - Unknown, 4 - Partial, 5 - Timeout, 6 - Not Found'. PHP_EOL;
        $output .= '# TYPE ' . $this->batch->getName() . '_component gauge' . PHP_EOL;
        foreach ($this->batch->getFragments() as $fragment) {
            $output .= $this->batch->getName() . '_component{component="' . $fragment->getName() . '",description="' . $fragment->getDescription() . '"} ' . strval($fragment->getStatusCode()) . PHP_EOL;
        }
        return $output;
    }

}
