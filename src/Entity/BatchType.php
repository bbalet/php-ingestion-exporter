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
     * @param mixed $name Batch name
     * @param mixed $description Batch description
     * @param mixed $id Internal unique identifier
     */
    function __construct($name, $description = null, $id = null) {
        $this->setName($name);
        $this->setDescription($description);
        $this->id = $id;
    }

}
