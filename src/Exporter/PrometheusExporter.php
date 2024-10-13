<?php

namespace Bbalet\PhpIngestionExporter\Exporter;

use Bbalet\PhpIngestionExporter\Exporter\Prometheus\BatchAsGauge;
use Bbalet\PhpIngestionExporter\Exporter\Prometheus\ListOfFragmentsTime;
use Bbalet\PhpIngestionExporter\Exporter\Prometheus\ListOfFragmentsStatus;
use Bbalet\PhpIngestionExporter\Database\AbstractDatabase;
use Bbalet\PhpIngestionExporter\Entity\Batch;

/**
 * Export the given collection of batches to prometheus format.
 * This class follows the Builder Design Pattern.
 * This class is designed in order to never throw an exception.
 */
class PrometheusExporter {

    /**
     * Collection of Exportable items
     * @var \ArrayObject<string, IExportableItem>
     */
    private $exportableItems;

    /**
     * List of batches
     * @var \ArrayObject<string, Batch>
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
        $this->exportableItems = new \ArrayObject();
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
        foreach ($this->exportableItems as $item) {
            $output .= $item->export() . PHP_EOL;
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
            $batch = $this->db->getLastBatch($name);
            if (!is_null($batch)) {
                $this->batches[$name] = $batch;
            } else {
                $this->selectedBatch == "";
            }
        }
        return $this;
    }

    /**
     * Export a batch as a gauge
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

    /**
     * Export the list of fragments of a batch and their execution times 
     * @return PrometheusExporter
     */
    public function ListOfFragmentsTime() {
        if (!$this->selectedBatch == "") {
            if (!is_null($this->batches[$this->selectedBatch])) {
                $this->exportableItems[] = new ListOfFragmentsTime($this->batches[$this->selectedBatch]);
            }
        }
        return $this;
    }

    /**
     * Export the list of fragments of a batch and their status code
     * @return PrometheusExporter
     */
    public function ListOfFragmentsStatus() {
        if (!$this->selectedBatch == "") {
            if (!is_null($this->batches[$this->selectedBatch])) {
                $this->exportableItems[] = new ListOfFragmentsStatus($this->batches[$this->selectedBatch]);
            }
        }
        return $this;
    }
}
