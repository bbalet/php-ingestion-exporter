<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Exception\BatchNotFoundException;

final class BatchNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessageIsAccurate(): void
    {
        $ex = new BatchNotFoundException();
        $this->assertSame('Batch not found', $ex->getMessage());
    }
}
