<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Exporter\Prometheus\ListOfFragmentsTime;

final class ListOfFragmentsTimeTest extends TestCase
{
    public function testExportTheListOfFragmentsOfABatchAndTheirExecutionTime(): void
    {
        $batch = new Batch("dummy_name", "Test of gauge");
        $batch->startFragment("file1", "file 1");
        usleep(20000); //sleep for 20ms
        $batch->stopFragment("file1");
        $batch->startFragment("file2", "file 2");
        usleep(20000); //sleep for 20ms
        $batch->stopFragment("file2");
        $listOfStatus = new ListOfFragmentsTime($batch);

        $expected = '# HELP dummy_name_component List of fragments execution times for the batch dummy_name' . PHP_EOL
                .   '# TYPE dummy_name_component gauge' . PHP_EOL
                .   'dummy_name_component{component="file1",description="file 1"} ' . strval($batch->getFragmentByName("file1")->getElaspedTime()) . PHP_EOL
                .   'dummy_name_component{component="file2",description="file 2"} ' . strval($batch->getFragmentByName("file2")->getElaspedTime()) . PHP_EOL;
        
        $this->assertSame($expected, $listOfStatus->export());
    }
}
