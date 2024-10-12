<?php

namespace Bbalet\PhpIngestionExporter\Exporter;

use Bbalet\PhpIngestionExporter\Exporter\Prometheus\BatchAsGauge;
use Bbalet\PhpIngestionExporter\Database\AbstractDatabase;

/**
 * Export the given collection of batches to prometheus format.
 * This class follows the Builder Design Pattern.
 * This class is designed in order to never throw an exception.
 */
class PrometheusExporter {

    /**
     * Collection of Exportable items
     * @var \ArrayObject
     */
    private $exportableItems;

    /**
     * List of batches
     * @var \ArrayObject
     */
    private $batches;

    /**
     * Database connection
     * @var AbstractDatabase
     */
    private $db;

    /**
     * Selected batch
     * @var string
     */
    private $selectedBatch;

    /**
     * Instantiate a PrometheusExporter object
     * @param AbstractDatabase $db connection to the database
     */
    function __construct(AbstractDatabase $db) {
        $this->db = $db;
    }

    /**
     * Return a string that could be displayed as a status page for Prometheus.
     * This class follow the builder design pattern. Supported types are:
     *  - BatchAsGauge
     *  - ListOfFragmentsTime
     *  - ListOfFragmentsStatus
     * @return string content that can be scraped by Prometheus collector
     */
    public function export() {
        //Iterate on the internal array and format Each object
        $output = "";
        if (!is_null($this->exportableItems)) {
            foreach ($this->exportableItems as $item) {
                $output .= $item->export() . PHP_EOL;
            }
        }
        return $output;
    }

    /**
     * Add a batch to the exporter
     * @param string $name
     * @return PrometheusExporter
     */
    public function lastExecutionOf($name) {
        $this->selectedBatch = $name;
        if (!$this->selectedBatch == "") {
            $this->batches[$name] = $this->db->getLastBatch($name);
        }
        return $this;
    }

    /**
     * Select a batch to export
     * @return PrometheusExporter
     */
    public function BatchAsGauge() {
        if (!$this->selectedBatch == "") {
            if (!is_null($this->batches[$this->selectedBatch])) {
                $this->exportableItems[] = new BatchAsGauge($this->batches[$this->selectedBatch]);
            }
        }
        return $this;
    }

}
