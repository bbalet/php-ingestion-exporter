<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Entity\Fragment;
use Bbalet\PhpIngestionExporter\Exporter\Prometheus\ListOfFragmentsStatus;

final class ListOfFragmentsStatusTest extends TestCase
{
    public function testExportTheListOfFragmentsOfABatchAndTheirStatusCode(): void
    {
        $batch = new Batch("dummy_name", "Test of gauge");
        $batch->startFragment("file1", "file 1");
        $batch->stopFragment("file1");
        $batch->startFragment("file2", "file 2");
        $batch->stopFragment("file2", Fragment::FAILURE);
        $listOfStatus = new ListOfFragmentsStatus($batch);

        $expected = '# HELP dummy_name_component List of fragments for the batch dummy_name. 0 - Success, 1 - Failure, 2 - Warning, 3 - Unknown, 4 - Partial, 5 - Timeout, 6 - Not Found' . PHP_EOL
                .   '# TYPE dummy_name_component gauge' . PHP_EOL
                .   'dummy_name_component{component="file1",description="file 1"} 0' . PHP_EOL
                .   'dummy_name_component{component="file2",description="file 2"} 1' . PHP_EOL;
        $this->assertSame($expected, $listOfStatus->export());
    }
}
