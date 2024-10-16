# php-ingestion-exporter

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![codecov](https://codecov.io/gh/bbalet/php-ingestion-exporter/graph/badge.svg?token=49L8O0L3Y5)](https://codecov.io/gh/bbalet/php-ingestion-exporter)
[![Maintainability](https://api.codeclimate.com/v1/badges/c90d88a8a791fd4f6080/maintainability)](https://codeclimate.com/github/bbalet/php-ingestion-exporter/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/c90d88a8a791fd4f6080/test_coverage)](https://codeclimate.com/github/bbalet/php-ingestion-exporter/test_coverage)

**php-ingestion-exporter** is a PHP library to export simple metrics about data ingestion to Prometheus with a status page (it can be easily extended to support additionnal use cases).

It is framework agnostic, doesn't use an ORM, has no dependency, and is designed to work with PHP7.2 up to PHP8.2.

For example, a data ingestion can be composed of one or many files and you want to keep track of the total time of the inggestion and (maybe) the detail for each file.
The sample code below will show you how it works:


    use Bbalet\PhpIngestionExporter\IngestionLogger;

    $logger = IngestionLogger::withPDOObject($pdoConnection);  //A PDO Connection object
    
    $batch = $logger->startBatch("batch_name", "Description of the batch");

    $file1 = $batch->startFragment("File 1");
    //Here ingest file 1
    $file1->stop();                                     //Stop to monitor the ingestion of the fragment

    $logger->getBatch("batch_name")->startFragment("File 2");   //Alternative way
    //Here ingest file 2
    $logger->getBatch("batch_name")->getFragment("File 2")->stop();

    $batch->stop();     //All fragments are stopped and metrics inserted into the DB

Later on, you might want to display the collected metrics of ingestion times for Prometheus:

    use Bbalet\PhpIngestionExporter\IngestionLogger;

    $logger = IngestionLogger::withConnectionString('mysql:host=localhost;dbname=test', $user, $pass);  //Alternative constructor

    $logger->prometheusExporter()->lastBatch("batch_name")->AsGauge()->export();

This will output an UTF-8 string suitable for Prometheus:

    # HELP batch_name Description of the batch
    # TYPE batch_name gauge
    batch_name 0.2354

Other exportation are possible such as histograms (e.g. last batches) and counters (e.g. total of ingestions, fragments)

The sample below shows how to export a more complex status (don't forget the call to *export* at the end of the builder):

    $logger->prometheusExporter()
            ->lastExecutionOf("batch_name")
            ->BatchAsGauge()
            ->ListOfFragmentsTime()
            ->export();


Afterward, you need to :
 - create a controller to return this status in a HTTP response.
 - configure Prometheus in order to scrape this status page.

## Prerequisites

php-ingestion-exporter requires a connection to an existing database with enough priviledges to create or alter tables int the schema, it can build the needed tables for you:

    use Bbalet\PhpIngestionExporter\PDOIngestionLogger;

    $logger = new PDOIngestionLogger($pdoConnection);
    

With the exception of SQLite for which it will create the database file if it doesn't exist.

It supports MySQL, SQLite, and PostGreSQL through PDO (you need the PHP extensions with the low level drivers).

## Configuration

Prefix for table names

## Utils

Migrate - Create or update the table in the schema
Rename batch name - will rename all existing batchs (replace)

## DB Model

pingexp_ is the default prefix for tables

params - key/value dictionnary for parameters (e.g. DB version)
batch_description - Name and description of a batch
batch - Id, name, and start/end nanoseconds timestamp
fragment - batch_id, name, and start/end nanoseconds timestamp

## Tests

    ./vendor/bin/phpunit --testdox tests
    ./vendor/bin/phpstan analyse src tests