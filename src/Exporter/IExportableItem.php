<?php

interface IExportableItem {

    /**
     * Return a string in the expected format for the exporter
     * @return string output format
     */
    public function toExportableFormat();
}