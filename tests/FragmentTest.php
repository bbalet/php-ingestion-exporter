<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Entity\Fragment;

final class FragmentTest extends TestCase
{
    public function testNameIsSanitized(): void
    {
        $batch = new Batch("dummy_name");
        $fragment = new Fragment($batch, "this is not safe <script>");
        $this->assertSame("this_is_not_safe_script_", $fragment->getName());
    }

    public function testEmptyNameIsReplacedWithDefaultValue(): void
    {
        $batch = new Batch("dummy_name");
        $fragment = new Fragment($batch, "");
        $this->assertSame("default_fragment", $fragment->getName());
        $fragment = new Fragment($batch, "_");
        $this->assertSame("default_fragment", $fragment->getName());
    }

    public function testDescriptionIsSanitized(): void
    {
        $batch = new Batch("dummy_name");
        $fragment = new Fragment($batch, "dummy name", "this is not safe <script>");
        $this->assertSame("this is not safe script ", $fragment->getDescription());
    }

    public function testEmptyDescriptionIsDerivatedFromNameValue(): void
    {
        $batch = new Batch("dummy_name");
        $fragment = new Fragment($batch, "dummy_name");
        $this->assertSame("No description provided for dummy name", $fragment->getDescription());
    }

    public function testElapsedTimeBetweenStartAndStopIsAccurate(): void
    {
        $batch = new Batch("dummy_name");
        $fragment = new Fragment($batch, "dummy_name");
        $fragment->start();
        usleep(20000); //sleep for 20ms
        $fragment->stop();
        $elapsedTime = $fragment->getElaspedTime();
        $this->assertEqualsWithDelta(0.020, $elapsedTime, 0.009);
    }

    public function testCountNumberOfLinesInFile(): void
    {
        $batch = new Batch("dummy_name");
        $data = "line 1". PHP_EOL . "line 2". PHP_EOL . "line 3";
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid();
        file_put_contents($tempFile, $data);
        $fragment = new Fragment($batch, "dummy_name");
        $fragment->countLines($tempFile);
        $this->assertSame(3, $fragment->getLinesCount());
    }

    public function testGetFileSize(): void
    {
        $batch = new Batch("dummy_name");
        $data = "line 1". PHP_EOL . "line 2". PHP_EOL . "line 3";
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid();
        file_put_contents($tempFile, $data);
        $fragment = Fragment::withFileStats($batch, "dummy_name", null, $tempFile);
        $expectedFileSize = 18 + (strlen(PHP_EOL) * 2);
        $this->assertSame($expectedFileSize, $fragment->getFileSize());
    }
}