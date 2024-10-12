<?php

namespace Bbalet\PhpIngestionExporter\Entity;

/**
 * A fragment is part of a batch that is used to measure its subparts
 */
class Fragment {
    use EntityTrait;
    use MeasurementTrait;

    /**
     * Size of the file in bytes
     *
     * @var int
     */
    private $fileSize;

    /**
     * Number of lines in the file
     *
     * @var int
     */
    private $linesCount;

    /**
     * Instanciate a Fragment which is most of the time a file
     * @param string $name Fragment name (this is sanitized)
     * @param string $description Fragment description, if null derivated from name
     */
    function __construct($name, $description = "") {
        $this->statusCode = self::UNKNOWN;
        $this->setName($name);
        $this->setDescription($description);
    }

    /**
     * Create a Fragment entity from the database
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $startTime
     * @param float $endTime
     * @param int $statusCode
     * @param int $fileSize
     * @param int $linesCount
     * @return Fragment
     */
    static function createFromDB($id, $name, $description, $startTime, $endTime, $statusCode, $fileSize, $linesCount) {
        $instance = new self($name, $description);
        $instance->id = $id;
        $instance->microStartTime = $startTime;
        $instance->microEndTime = $endTime;
        $instance->statusCode = $statusCode;
        $instance->fileSize = $fileSize;
        $instance->linesCount = $linesCount;
        return $instance;
    }

    /**
     * Instanciate a Fragment with file stats
     * @param string $name Fragment name (this is sanitized)
     * @param string $description Fragment description, if null derivated from name
     * @param string $filePath path to the file
     * @return Fragment
     */
    public static function withFileStats($filePath, $name, $description = null) {
        $instance = new self($name, $description);
        $instance->linesCount = $instance->countLines($filePath);
        $instance->fileSize = filesize($filePath);
        return $instance;
    }

    /**
     * End the measurement of a fragment, save the current time in microseconds
     * @param int $statusCode status code of the fragment
     * @param int $fileSize size of the file in bytes
     * @param int $linesCount number of lines in the file
     * @return void
     */
    public function stop($statusCode = self::SUCCESS, $fileSize = 0, $linesCount = 0) {
        $this->statusCode = $statusCode;
        $this->fileSize = $fileSize;
        $this->linesCount = $linesCount;
        $this->microEndTime = microtime(true);
    }

    /**
     * Count the number of lines in a file
     * @param string $filePath path to the file
     * @param string $endOfLine character(s) used to mark the end of a line
     * @return int number of lines in the file
     */
    public function countLines($filePath, $endOfLine = PHP_EOL) {
        $linecount = 0;
        $handle = fopen($filePath, "r");
        while(!feof($handle)){
          $line = fgets($handle, 4096);
          $linecount = $linecount + substr_count($line, $endOfLine);
        }
        fclose($handle);
        $this->linesCount = $linecount + 1;
        return $linecount;
    }

    /**
     * Return the number of lines in the file
     * @return int number of lines
     */
    public function getLinesCount() {
        return $this->linesCount;
    }

    /**
     * Set the number of lines in the file
     * @param int $linesCount number of lines
     * @return void
     */
    public function setLinesCount($linesCount) {
        $this->linesCount = $linesCount;
    }

    /**
     * Return the size of the file in bytes
     * @return int size of the file
     */
    public function getFileSize() {
        return $this->fileSize;
    }

    /**
     * Set the size of the file in bytes
     * @param int $fileSize size of the file
     * @return void
     */
    public function setFileSize($fileSize) {
        $this->fileSize = $fileSize;
    }
}
