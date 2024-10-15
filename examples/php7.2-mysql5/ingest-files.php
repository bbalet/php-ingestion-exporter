<?php
//The pupose of this script is to ingest two text files
//This wiil be used by the minimal web server in order to expose the metrics to Prometheus
//this script is called by the docker compose command
require './vendor/autoload.php';

use Bbalet\PhpIngestionExporter\IngestionLogger;

//The second parameter means that we will setup the database schema
$connectionString = 'mysql:host=127.0.0.1;dbname=test_db;user=user;password=password';
$logger = IngestionLogger::withConnectionString($connectionString, true);

$batch = $logger->startBatch("example_php", "Example of ingestion with ");

//Simulate the ingestion of the files
$logger->getBatch("example_php")->startFragmentWithFileStats("./file-with-three-lines.txt", "file1");
usleep(20000); //sleep for 20ms
$logger->getBatch("example_php")->stopFragment("file1");
$logger->getBatch("example_php")->startFragmentWithFileStats("./file-with-two-lines.txt", "file1");
usleep(20000); //sleep for 20ms
$logger->getBatch("example_php")->stopFragment("file1");

//stop the batch (and persist the result into the database)
$logger->stopBatch("example_php");
