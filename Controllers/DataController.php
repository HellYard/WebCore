<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 5/10/2016
 * Time: 6:50 PM
 * Version: Beta 1
 * Last Modified: 5/10/2016 at 6:50 PM
 * Last Modified by Daniel Vidmar.
 */
class DataController
{
    private $connections = array();
    private $data = array();
    private $queries = array();
    private $results = array();
    private $current = 0;
    private $previous;

    public function create($engine = "mysql", $host, $db, $user, $pass) {
        $dsn = $engine.":dbname=".$db.";host=".$host;
        try {
            $this->connections[] = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            $this->current = count($this->connections) - 1;
        } catch(PDOException $exception) {
            trigger_error("Error while attempting to create new connection!");
        }
        return $this->current;
    }

    public function close($id) {
        $this->connections[$id] = null;
    }

    public function change_connection($id) {
        $this->current = $id;
    }

    public function get_connection() {
        return $this->connections[$this->current];
    }

    public function query($query_str, $cache = false, $prepared = false, $values = array()) {
        $connection = $this->get_connection();
        if($connection instanceof PDO) {
            $result = false;
            if($prepared) {
                $statement = $connection->prepare($query_str);
                $result = $statement->execute($values);
            } else {
                $result = $connection->query($query_str);
            }

            if(!$result) {
                trigger_error("Error occured while executing query!");
                return -1;
            }

            if($cache) {
                $this->queries[] = $result;
                return count($this->queries) - 1;
            }
            $this->previous = $result;
            return 0;
        }
        return -1;
    }

    public function store_data($data) {
        $this->data = $data;
        return count($this->data) - 1;
    }

    public function get_data($id) {
        return $this->data[$id];
    }

    public function cache_result($result) {
        $this->results[$result];
        return count($this->results) - 1;
    }

    public function get_result($id) {
        return $this->results[$id];
    }

    public function __construct() {

    }

    public function __destruct() {
        foreach($this->connections as $connection) {
            $connection = null;
        }
    }
}