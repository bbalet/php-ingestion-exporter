<?php

namespace Bbalet\PhpIngestionExporter\Database;

/**
 * Factory to create a database object from a PDO connection or a connection string
 */
final class DatabaseFactory
{
    /**
     * Singleton instance
     * @var DatabaseFactory
     */
    private static $instance;

    /**
     * Database connection
     * @var AbstractDatabase
     */
    private static $database;

    /**
     * Private constructor to prevent instantiation
     */
    private function __construct()
    {

    }

    /**
     * Get the singleton instance
     * @return DatabaseFactory
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * Get a database object from a connection string
     * @param string $connectionString Connection string to the database
     * @param bool $migrate whether to migrate the schema or not
     * @param string $prefix table name prefix
     * @return AbstractDatabase
     */
    public static function getDatabaseFromConnectionString($connectionString, $migrate = true, $prefix = 'pingexp') {
        $pdoConnection = new \PDO($connectionString);
        return static::getDatabaseFromPDOObject($pdoConnection, $migrate, $prefix);
    }

    /**
     * Get a database object from a connection string
     * @param \PDO $pdoConnection Active PDO Connection to the database
     * @param bool $migrate whether to migrate the schema or not
     * @param string $prefix table name prefix
     * @return AbstractDatabase
     * @throws \Exception
     */
    public static function getDatabaseFromPDOObject($pdoConnection, $migrate = true, $prefix = 'pingexp')
    {
        $instance = static::getInstance();
        $driver = $pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if (is_null($driver)) {
            throw new \Exception("Invalid PDO connection");
        }
        if (!is_string($driver)) {
            throw new \Exception("Unprocessable ATTR_DRIVER_NAME value");
        }
        switch ($driver) {
            case 'sqlite':
                static::$database = new SqliteDatabase($pdoConnection, $migrate, $prefix);
                break;
            case 'sqlsrv':
                static::$database = new SQLServerDatabase($pdoConnection, $migrate, $prefix);
                break;
            case 'mysql':
                static::$database = new MySQLDatabase($pdoConnection, $migrate, $prefix);
                break;
            case 'pgsql':
                static::$database = new PostgresDatabase($pdoConnection, $migrate, $prefix);
                break;
            //Other drivers are oci, cubrid, odbc, firebird...
            default:
                throw new \Exception("Unsupported driver: $driver");
        }
        return static::$database;
    }
    
}
