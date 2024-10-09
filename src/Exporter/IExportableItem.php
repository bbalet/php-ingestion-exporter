<?php

namespace Bbalet\PhpIngestionExporter\Exporter;

interface IExportableItem {

    /**
     * Return a string in the expected format for the exporter
     * @return string output format
     */
    public function export();
}