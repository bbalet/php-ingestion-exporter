<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Parameter;

final class ParameterTest extends TestCase
{
    public function testGetAndSetParameter(): void
    {
        $parameter = new Parameter("key", "value");
        $this->assertSame("key", $parameter->getKey());
        $this->assertSame("value", $parameter->getValue());
    }
}
