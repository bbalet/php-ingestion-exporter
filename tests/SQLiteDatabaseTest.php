<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Database\DatabaseFactory;
use Bbalet\PhpIngestionExporter\Entity\BatchType;
use Bbalet\PhpIngestionExporter\Entity\Batch;

final class SQLiteDatabaseTest extends TestCase
{

    public function testSetAndGetParameterFromDatabase(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $db->setParameter("test", "1024");
        $params = $db->getParameters();
        $this->assertSame("1024", $params["test"]);
    }

    public function testSetAndGetBatchType(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $newBatchType = new BatchType("test","description");
        $db->setBatchType($newBatchType);
        $batchType = $db->getBatchType("test");
        $this->assertSame("test", $batchType->getName());
        $this->assertSame("description", $batchType->getDescription());
        $this->assertGreaterThan(0, $batchType->getId());
    }

    public function testInsertMultipleBatchesButGetTheLastExecutedBatch(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $batch1 = new Batch("batch");
        $batch2 = new Batch("batch");
        $batch1->start();
        $db->setBatch($batch1);
        $batch2->start();
        $db->setBatch($batch2);        
        $lastBatch = $db->getLastBatch("batch");
        //Depending on the execution platform, the floats may differ in precision
        $this->assertEqualsWithDelta($batch2->getStartTime(), $lastBatch->getStartTime(), 0.001);
    }

    public function testPersistABatchWithMultipleFragments(): void
    {
        $tempDB = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid() . ".sqlite3";
        $consStr = "sqlite:" . $tempDB;
        $db = DatabaseFactory::getDatabaseFromConnectionString($consStr);
        $batch = new Batch("batch");
        $batch->startFragment("fragment1");
        $batch->startFragmentWithFileStats(__FILE__, "fragment2");
        $db->setBatch($batch);
        $lastBatch = $db->getLastBatch("batch");
        $fragments = $lastBatch->getFragments();
        $this->assertSame(2, count($fragments));
        $this->assertSame("fragment1", $fragments["fragment1"]->getName());
        $this->assertSame("fragment2", $fragments["fragment2"]->getName());
        $this->assertGreaterThan(0, $fragments["fragment2"]->getFileSize());
        $this->assertGreaterThan(0, $fragments["fragment2"]->getLinesCount());
    }
}