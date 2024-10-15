<?php
//This example export ingestion metrics for Prometheus
//The status page is served at the root of the web server
//i.e. http://localhost:8080/
require './vendor/autoload.php';

//The two files have been ingested when building the Docker image
//So the database is already seeded (see the Dockerfile)

use Bbalet\PhpIngestionExporter\IngestionLogger;

//Note that the schema was setup by the ingestion script, so we don't need to check if the tables are here
//And it would add testing queries that we don't need
$connectionString = 'mysql:host=127.0.0.1;dbname=test_db;user=user;password=password';
$logger = IngestionLogger::withConnectionString($connectionString, false);

$output = $logger->getPrometheusExporter()
        ->lastExecutionOf("example_php")
        ->BatchAsGauge()
        ->ListOfFragmentsTime()
        ->export();

echo $output;
