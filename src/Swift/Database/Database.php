<?php

namespace Tawn33y\Swift\Database;

use PDO;
use PDOException;
use Tawn33y\Swift\Contracts\ConfigurationInterface;
use Tawn33y\Swift\Exceptions\ConnectionException;

class Database
{
    private $connection;

    /**
     * Database constructor.
     *
     * @param ConfigurationInterface $configuration
     *
     * @throws ConnectionException
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        try {
            $connection = new PDO(
                "mysql:host=" . $configuration->get('host') . ";dbname=" . $configuration->get('database'),
                $configuration->get('username'),
                $configuration->get('password')
            );

            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->connection = $connection;
        } catch (PDOException $e) {
            throw new ConnectionException("Sorry we're experiencing connection problems: " . $e->getMessage());
        }
    }

    /**
     * Get the database connection.
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
