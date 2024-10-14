<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Entity\Batch;
use Bbalet\PhpIngestionExporter\Entity\Fragment;

final class FragmentTest extends TestCase
{
    public function testNameIsSanitized(): void
    {
        $fragment = new Fragment("this is not safe <script>");
        $this->assertSame("this_is_not_safe_script_", $fragment->getName());
    }

    public function testEmptyNameIsReplacedWithDefaultValue(): void
    {
        $fragment = new Fragment("");
        $this->assertSame("default_fragment", $fragment->getName());
        $fragment = new Fragment("_");
        $this->assertSame("default_fragment", $fragment->getName());
    }

    public function testDescriptionIsSanitized(): void
    {
        $fragment = new Fragment("dummy name", "this is not safe <script>");
        $this->assertSame("this is not safe script ", $fragment->getDescription());
    }

    public function testEmptyDescriptionIsDerivatedFromNameValue(): void
    {
        $fragment = new Fragment("dummy_name");
        $this->assertSame("No description provided for dummy name", $fragment->getDescription());
    }

    public function testElapsedTimeBetweenStartAndStopIsAccurate(): void
    {
        $fragment = new Fragment("dummy_name");
        $fragment->start();
        usleep(20000); //sleep for 20ms
        $fragment->stop();
        $elapsedTime = $fragment->getElaspedTime();
        $this->assertEqualsWithDelta(0.020, $elapsedTime, 0.009);
    }

    public function testCountNumberOfLinesInFile(): void
    {
        $data = "line 1". PHP_EOL . "line 2". PHP_EOL . "line 3";
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid();
        file_put_contents($tempFile, $data);
        $fragment = new Fragment("dummy_name");
        $fragment->countLines($tempFile);
        $this->assertSame(3, $fragment->getLinesCount());
    }

    public function testGetFileSize(): void
    {
        $data = "line 1". PHP_EOL . "line 2". PHP_EOL . "line 3";
        $tempFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR. uniqid();
        file_put_contents($tempFile, $data);
        $fragment = Fragment::withFileStats($tempFile, "dummy_name");
        $expectedFileSize = 18 + (strlen(PHP_EOL) * 2);
        $this->assertSame($expectedFileSize, $fragment->getFileSize());
    }

    public function testSetAndGetLinesCount(): void
    {
        $fragment = new Fragment("dummy_name");
        $fragment->setLinesCount(10);
        $this->assertSame(10, $fragment->getLinesCount());
    }

    public function testSetAndGetFileSize(): void
    {
        $fragment = new Fragment("dummy_name");
        $fragment->setFileSize(100);
        $this->assertSame(100, $fragment->getFileSize());
    }

}