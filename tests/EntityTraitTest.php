<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\EntityTrait;

final class EntityTraitTest extends TestCase
{
    use EntityTrait;

    public function testGetAndSetId(): void
    {
        $this->setId(2);
        $this->assertSame(2, $this->getId());
    }
}
