<?php
require_once __DIR__ . "/../config/config.php";

/**
 * Generic data access object. Every entity DAO extends this class and only has
 * to say which table it works with and what the table's primary key is called
 * (GameVault uses user_id, game_id, ... instead of a plain "id").
 */
class BaseDao
{
    protected $connection;
    private $table_name;
    private $primary_key;

    public function __construct($table_name, $primary_key = "id")
    {
        $this->table_name = $table_name;
        $this->primary_key = $primary_key;
        try {
            $this->connection = new PDO(
                "mysql:host=" . Config::DB_HOST() . ";dbname=" . Config::DB_NAME() . ";port=" . Config::DB_PORT() . ";charset=utf8mb4",
                Config::DB_USER(),
                Config::DB_PASSWORD(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    protected function query($query, $params)
    {
        $stmt = $this->connection->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function query_unique($query, $params)
    {
        $results = $this->query($query, $params);
        return reset($results);
    }

    /**
     * Column names cannot be bound as parameters, so they are checked here
     * before being placed into a query. Values are always bound.
     */
    private function assert_column($column)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $column)) {
            throw new InvalidArgumentException("Invalid column name: " . $column);
        }
    }

    /**
     * Insert a new row. $entity is an associative array column => value.
     * Returns the entity with the generated primary key added.
     */
    public function add($entity)
    {
        $columns = array_keys($entity);
        foreach ($columns as $column) {
            $this->assert_column($column);
        }
        $query = "INSERT INTO " . $this->table_name
            . " (" . implode(", ", $columns) . ")"
            . " VALUES (:" . implode(", :", $columns) . ")";

        $stmt = $this->connection->prepare($query);
        $stmt->execute($entity);
        $entity[$this->primary_key] = $this->connection->lastInsertId();
        return $entity;
    }

    /**
     * Update the row whose primary key (or $id_column) equals $id.
     */
    public function update($entity, $id, $id_column = null)
    {
        $id_column = $id_column ?? $this->primary_key;
        $this->assert_column($id_column);

        $assignments = [];
        foreach (array_keys($entity) as $column) {
            $this->assert_column($column);
            $assignments[] = $column . "=:" . $column;
        }
        $query = "UPDATE " . $this->table_name
            . " SET " . implode(", ", $assignments)
            . " WHERE " . $id_column . " = :where_id";

        $stmt = $this->connection->prepare($query);
        $entity['where_id'] = $id;
        $stmt->execute($entity);
        unset($entity['where_id']);
        $entity[$id_column] = $id;
        return $entity;
    }

    /**
     * Delete the row whose primary key equals $id.
     */
    public function delete($id)
    {
        $stmt = $this->connection->prepare(
            "DELETE FROM " . $this->table_name . " WHERE " . $this->primary_key . " = :id"
        );
        $stmt->bindValue(':id', $id); // bound parameter, prevents SQL injection
        $stmt->execute();
    }

    public function get_all()
    {
        return $this->query('SELECT * FROM ' . $this->table_name, []);
    }

    public function get_by_id($id)
    {
        return $this->query_unique(
            'SELECT * FROM ' . $this->table_name . ' WHERE ' . $this->primary_key . ' = :id',
            ['id' => $id]
        );
    }
}
