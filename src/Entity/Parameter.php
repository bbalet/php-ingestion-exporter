<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * Parameter
 */
class Parameter {

    /**
     * Name of the parameter
     * @var string
     */
    private $key;

    /**
     * Value of the parameter
     * @var string
     */
    private $value;

    /**
     * Instanciate a parameter
     * @param string $key parameter name
     * @param string $value parameter value
     */
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
     * @return string value
     */
    public function getValue() {
        return $this->value;
    }

}
