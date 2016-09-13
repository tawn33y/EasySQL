<?php

namespace Tawn33y\Swift;

use PDO;
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
     * @var PDO
     */
    private $connection;

    /**
     * The table name.
     *
     * @var string
     */
    private $table;


    /**
     * @var string
     */
    private $statement = '';

    /**
     * Builder constructor.
     *
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
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
        $this->statement = '';

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

        $statement = $this->connection->prepare($query);

        $statement->execute();
    }

    /**
     * Add constraints to the query.
     *
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function where($column, $operator, $value)
    {
        $this->statement .= ' WHERE `' . $column . '`' . $operator . $value;

        return $this;
    }

    /**
     * Get the query in a particular order.
     *
     * @param        $column
     * @param string $mode
     *
     * @return $this
     */
    public function orderBy($column, $mode = 'ASC')
    {
        $this->statement .= ' ORDER BY `' . $column . '` ' . $mode;

        return $this;
    }

    /**
     * Execute the query getting the columns that you want.
     *
     * @param array $columns
     *
     * @return array
     */
    public function get(array $columns = ['*'])
    {
        $query = 'SELECT ' . implode(',', $columns) . ' FROM ' . $this->table . $this->statement;

        $statement = $this->connection->prepare($query);

        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_OBJ);
    }
}
