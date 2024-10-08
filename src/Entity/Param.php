<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * Parameter
 */
class Param {

    private $key;


    private $value;


    public function __construct($key, $value) {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * Return the key
     * @return string key name
     */
    public function getKey() {
        return $this->key;
    }
    
    /**
     * Return the value
     * @return mixed value
     */
    public function getValue() {
        return $this->value;
    }

}
