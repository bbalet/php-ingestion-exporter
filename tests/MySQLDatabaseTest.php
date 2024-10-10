<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Bbalet\PhpIngestionExporter\Database\DatabaseFactory;

final class MySQLDatabaseTest extends TestCase
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
}