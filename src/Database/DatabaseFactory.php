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
     * @param string $prefix table name prefix
     * @return AbstractDatabase
     */
    public static function getDatabaseFromConnectionString($connectionString, $prefix = 'pingexp_') {
        $pdoConnection = new \PDO($connectionString);
        return static::getDatabaseFromPDOObject($pdoConnection, $prefix);
    }

    /**
     * Get a database object from a connection string
     * @param \PDO $pdoConnection Active PDO Connection to the database
     * @param string $prefix table name prefix
     * @return AbstractDatabase
     */
    public static function getDatabaseFromPDOObject($pdoConnection, $prefix = 'pingexp_')
    {
        $instance = static::getInstance();
        $driver = $pdoConnection->getAttribute(\PDO::ATTR_DRIVER_NAME);
        switch ($driver) {
            case 'sqlite':
                static::$database = new SqliteDatabase($pdoConnection, $prefix);
                break;
            /*case 'sqlsrv':
                static::$database = new SqlServerDatabase($pdoConnection);
                break;
            case 'mysql':
                static::$database = new MysqlDatabase($pdoConnection);
                break;
            case 'pgsql':
                static::$database = new PostgresDatabase($pdoConnection);
                break;
            case 'oci':
                static::$database = new OracleDatabase($pdoConnection);
                break;
            case 'cubrid':
                static::$database = new CubridDatabase($pdoConnection);
                break;
            case 'odbc':
                static::$database = new OdbcDatabase($pdoConnection);
                break;
            case 'firebird':
                static::$database = new FirebirdDatabase($pdoConnection);
                break;*/
            default:
                throw new \Exception("Unsupported driver: $driver");
        }
        return static::$database;
    }
    
}
