<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Entity\Fragment;
use Bbalet\PhpIngestionExporter\Database\DatabaseFactory;
use Bbalet\PhpIngestionExporter\Exporter\PrometheusExporter;

final class PrometheusExporterTest extends TestCase
{
    public function testExportABatchToAGaugeMetric(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $exporter = new PrometheusExporter($db);

        $batch = new Batch("batch_name", "Test of gauge exporter");
        $batch->start();
        usleep(20000); //sleep for 20ms
        $batch->stop();
        
        $db->setBatch($batch);
        $storedBatch = $db->getLastBatch("batch_name");
        $elapsedTime = strval($storedBatch->getElaspedTime());

        $output = $exporter->lastExecutionOf("batch_name")->BatchAsGauge()->export();

        $expected = '# HELP batch_name_duration_seconds Test of gauge exporter' . PHP_EOL
                .   '# TYPE batch_name_duration_seconds gauge' . PHP_EOL
                .   'batch_name_duration_seconds ' . $elapsedTime . PHP_EOL . PHP_EOL;

        $this->assertSame($expected, $output);
    }

    public function testtestExportABatchToAGaugeMetricAndItsFragmentsExecutionTimes(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $exporter = new PrometheusExporter($db);

        $batch = new Batch("batch_name", "Test of gauge");
        $batch->startFragment("file1", "file 1");
        usleep(20000); //sleep for 20ms
        $batch->stopFragment("file1");
        $batch->startFragment("file2", "file 2");
        usleep(20000); //sleep for 20ms
        $batch->stopFragment("file2");
        $batch->stop();

        $db->setBatch($batch);
        $storedBatch = $db->getLastBatch("batch_name");
        $elapsedTime = strval($storedBatch->getElaspedTime());

        $output = $exporter->lastExecutionOf("batch_name")->BatchAsGauge()->ListOfFragmentsTime()->export();


        $expected = '# HELP batch_name_duration_seconds Test of gauge' . PHP_EOL
                .   '# TYPE batch_name_duration_seconds gauge' . PHP_EOL
                .   'batch_name_duration_seconds ' . $elapsedTime . PHP_EOL
                .   PHP_EOL
                .   '# HELP batch_name_component List of fragments execution times for the batch batch_name' . PHP_EOL
                .   '# TYPE batch_name_component gauge' . PHP_EOL
                .   'batch_name_component{component="file1",description="file 1"} ' . strval($storedBatch->getFragmentByName("file1")->getElaspedTime()) . PHP_EOL
                .   'batch_name_component{component="file2",description="file 2"} ' . strval($storedBatch->getFragmentByName("file2")->getElaspedTime()) . PHP_EOL
                . PHP_EOL;
        
        $this->assertSame($expected, $output);
    }

    public function testtestExportABatchToAGaugeMetricAndItsFragmentsStatusCodes(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $exporter = new PrometheusExporter($db);

        $batch = new Batch("batch_name", "Test of gauge");
        $batch->startFragment("file1", "file 1");        
        $batch->startFragment("file2", "file 2");
        usleep(20000); //sleep for 20ms
        $batch->stopFragment("file1");
        $batch->stopFragment("file2", Fragment::FAILURE);
        $batch->stop();
        $db->setBatch($batch);
        $storedBatch = $db->getLastBatch("batch_name");
        $elapsedTime = strval($storedBatch->getElaspedTime());

        $output = $exporter->lastExecutionOf("batch_name")->BatchAsGauge()->ListOfFragmentsStatus()->export();

        $expected = '# HELP batch_name_duration_seconds Test of gauge' . PHP_EOL
                .   '# TYPE batch_name_duration_seconds gauge' . PHP_EOL
                .   'batch_name_duration_seconds ' . $elapsedTime . PHP_EOL
                .   PHP_EOL
                .   '# HELP batch_name_component List of fragments status for the batch batch_name. 0 - Success, 1 - Failure, 2 - Warning, 3 - Unknown, 4 - Partial, 5 - Timeout, 6 - Not Found' . PHP_EOL
                .   '# TYPE batch_name_component gauge' . PHP_EOL
                .   'batch_name_component{component="file1",description="file 1"} 0' . PHP_EOL
                .   'batch_name_component{component="file2",description="file 2"} 1' . PHP_EOL
                . PHP_EOL;

        $this->assertSame($expected, $output);
    }
}