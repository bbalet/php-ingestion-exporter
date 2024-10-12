<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;

final class BatchTest extends TestCase
{
    public function testNameIsSanitized(): void
    {
        $batch = new Batch("this is not safe <script>");
        $this->assertSame("this_is_not_safe_script_", $batch->getName());
    }

    public function testEmptyNameIsReplacedWithDefaultValue(): void
    {
        $batch = new Batch("");
        $this->assertSame("default_batch", $batch->getName());
        $batch = new Batch("_");
        $this->assertSame("default_batch", $batch->getName());
    }

    public function testDescriptionIsSanitized(): void
    {
        $batch = new Batch("dummy name", "this is not safe <script>");
        $this->assertSame("this is not safe script ", $batch->getDescription());
    }

    public function testEmptyDescriptionIsDerivatedFromNameValue(): void
    {
        $batch = new Batch("dummy_name");
        $this->assertSame("No description provided for dummy name", $batch->getDescription());
    }

    public function testElapsedTimeBetweenStartAndStopIsAccurate(): void
    {
        $batch = new Batch("dummy_name");
        $batch->start();
        usleep(20000); //sleep for 20ms
        $batch->stop();
        $elapsedTime = $batch->getElaspedTime();
        $this->assertEqualsWithDelta(0.020, $elapsedTime, 0.009);
    }

    public function testGetFragmentByNameInFragmentsCollection(): void
    {
        $batch = new Batch("dummy_name");
        $batch->startFragment("file1");
        $fragmentFromCol = $batch->getFragmentByName("file1");
        $this->assertSame("file1", $fragmentFromCol->getName());
    }

    public function testEndingABatchMustStopAllItsChildrenFragments(): void
    {
        $batch = new Batch("dummy_name");
        $batch->startFragment("file1");
        $batch->startFragment("file2");
        usleep(20000); //sleep for 20ms
        $batch->stop();
        $elapsedTime1 = $batch->getFragmentByName("file1")->getElaspedTime();
        $elapsedTime2 = $batch->getFragmentByName("file1")->getElaspedTime();
        $this->assertEqualsWithDelta(0.020, $elapsedTime1, 0.009);
        $this->assertEqualsWithDelta(0.020, $elapsedTime2, 0.009);
    }

    public function testDuplicatingAFragmentJustEraseTheFirstOne(): void
    {
        $batch = new Batch("dummy_name");
        $batch->startFragment("file1", "description");
        $batch->startFragment("file1", "new");
        $fragmentFromCol = $batch->getFragmentByName("file1");
        $this->assertSame("new", $fragmentFromCol->getDescription());
    }

    public function testIterateOnFragmentsAndDisplayTheirNames(): void
    {
        $batch = new Batch("dummy_name");
        $batch->startFragment("file1");
        $batch->startFragment("file2");
        $fragments = $batch->getFragments();
        foreach ($fragments as $fragment) {
            $this->assertContains($fragment->getName(), ["file1", "file2"]);
        }
        
    }

}