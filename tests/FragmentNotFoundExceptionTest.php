<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Exception\FragmentNotFoundException;

final class FragmentNotFoundExceptionTest extends TestCase
{
    public function testExceptionMessageIsAccurate(): void
    {
        $ex = new FragmentNotFoundException();
        $this->assertSame('Fragment not found in the batch', $ex->getMessage());
    }
}
