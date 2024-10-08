<?php

namespace Bbalet\PhpIngestionExporter\Entity;

trait EntityTrait {
    /**
     * Unique identifier that will be stored in DB
     * @var int
     */
    protected $id;

    /**
     * Name of the entity
     * @var string
     */
    protected $name;

    /**
     * Description of the entity
     * @var string
     */
    protected $description;

    /**
     * Safely set the entity name
     * Replace any sequence of non-alphanumeric characters and replaces it with a single underscore.
     * Doing this way avoid having two consecutive underscores in the attribute
     * @param string $name Entity name
     * @return void
     */
    protected function setName($name) {
        $this->name = preg_replace('/[^a-z0-9]+/', '_', strtolower($name));
        if ($this->name === '' or $this->name === '_') {
            $reflect = new \ReflectionClass($this);
            $this->name = 'default_' . strtolower($reflect->getShortName());
        }
    }

    /**
     * Safely set the description by replacing any non-alphanumeric characters by a space
     * Provide a default description based on the name attribute if the parameter is null
     * @param string $description
     * @return void
     */
    protected function setDescription($description) {
        if (is_null($description)) {
            $this->description = 'No description provided for ' . str_replace("_"," ", $this->name);
        } else {
            $this->description = preg_replace('/[^a-z0-9]+/', ' ', strtolower($description));
        }
    }

    /**
     * Return the unique identifier of the entity
     * @return int internal id (in the DB)
     */
    public function getId(){
        return $this->id;
    }

    /**
     * Return the name of the entity
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Return the description of the entity
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }
}
