<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\MeasurementTrait;

final class MeasurementTraitTest extends TestCase
{
    use MeasurementTrait;

    public function testExplainAllStatusCodes(): void
    {
        $expected = '0 - Success, 1 - Failure, 2 - Warning, 3 - Unknown, 4 - Partial, 5 - Timeout, 6 - Not Found';
        $this->assertSame($expected, $this->describeListOfStatusCodes());
    }
}