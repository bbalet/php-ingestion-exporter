<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Exporter\Prometheus\BatchAsGauge;

final class BatchAsGaugeTest extends TestCase
{
    public function testExportABatchToPrometheusGaugeMetric(): void
    {
        $batch = new Batch("dummy_name", "Test of gauge");
        $batch->start();
        usleep(20000); //sleep for 20ms
        $batch->stop();
        $elapsedTime = strval($batch->getElaspedTime());
        $batchAsGauge = new BatchAsGauge($batch);

        $expected = 'HELP dummy_name_duration_seconds Test of gauge' . PHP_EOL
                .   'TYPE dummy_name_duration_seconds gauge' . PHP_EOL
                .   'dummy_name_duration_seconds ' . $elapsedTime . PHP_EOL;

        $this->assertSame($expected, $batchAsGauge->export());
    }
}