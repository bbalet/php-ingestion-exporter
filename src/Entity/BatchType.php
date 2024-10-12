<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * A BatchType is used in the concrete storage to qualify a batch with a description
 */
class BatchType {
    use EntityTrait;

    /**
     * Instantiate a batch type : should not be called directly but through the batch children object
     * @see Batch::__construct
     * @param string $name Batch name
     * @param string $description Batch description
     * @param int $id Internal unique identifier
     */
    function __construct($name, $description = "", $id = null) {
        $this->setName($name);
        $this->setDescription($description);
        $this->id = $id;
    }

}
