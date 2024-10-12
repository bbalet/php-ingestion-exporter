<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
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
}