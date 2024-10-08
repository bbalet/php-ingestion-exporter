<?php

namespace Bbalet\PhpIngestionExporter\Exporter;

use Bbalet\PhpIngestionExporter\Exporter\IExportableItem;


/**
 * Export the given collection of batches to prometheus format.
 * This class follows the Builder Design Pattern.
 */
class PrometheusExporter {

    /**
     * Collection of Exportable items
     * @var 
     */
    private $exportableItems;

    /**
     * Instantiate a PrometheusExporter object
     * @param mixed $batches collection of batches
     */
    function __construct($batches) {
        $this->batches = $batches;
    }

    /**
     * Return a string that could be displayed as a status page for Prometheus.
     * This class follow the builder design pattern. Supported types are:
     *  - BatchAsGauge
     *  - NumberOfBatches
     *  - NumberOfFragments implements IExportableItem ??
     *  - ListOfBatches
     *  - ListOfFragments
     * @return string content that can be scraped by Prometheus collector
     */
    public function export() {
        //Iterate on the internal array and format Each object
        return "";
    }

    public function lastExecutionOfBatchAsGauge() {
        return $this;
    }

}
