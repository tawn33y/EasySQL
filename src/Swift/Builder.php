<?php

namespace Tawn33y\Swift;

use Tawn33y\Swift\Database\Database;

/**
 * Class Builder
 *
 * @category PHP
 * @package  Tawn33y\Swift
 */
class Builder
{
    /**
     * @var Database
     */
    private $database;

    /**
     * The table name.
     *
     * @var string
     */
    private $table;

    /**
     * Builder constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    /**
     * Sets the table to be used for the transactions.
     *
     * @param $table
     *
     * @return $this
     */
    public function table($table)
    {
        $this->table = $table;

        return $this;
    }

    /**
     * This function alters the existing table and adds a new column.
     * usage: $builder->table('users')->addColumn('password', 'string');
     *
     * @param $name string Name of the new column to be inserted
     * @param $type string Type of the column to be inserted e.g INT
     */
    public function addColumn($name, $type)
    {
        $query = 'ALTER TABLE `' . $this->table .'` ADD `' . $name .'` ` '. $type . '`';

        $statement = $this->database->prepare($query);

        $statement->execute();
    }
}
