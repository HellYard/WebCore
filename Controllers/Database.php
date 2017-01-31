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
class DataController implements Controller {

  /*
   * TODO/GOALS:
   *  ` - done
   *  * - WIP
   * All DSN options should be supported `
   *   Driver invocation `
   *   URI invocation `
   *   Alias `
   * Allow for caching of queries, and results `
   * Allow support for as many DB Engines as possible *
   * Allow for multiple connections `
   * Allow for prepared statements `
   * Provide utility functions for common queries *
   * Documentation *
   *
   * TODO/FUTURE:
   * Ability to convert DBs between various engines.
   */
  private $pre_defined = array(
    "default" => array(
      "dsn" => "{engine}:dbname={db};host={host}",
      "table" => array(
        "create" => "CREATE TABLE IF NOT EXISTS `{name}` (".
          "{columns}".
          ");",
        "DROP" => "DROP TABLE `{name}`;",
        "DELETE" => "DELETE FROM `{table}` WHERE {columns}",
        "SELECT {rows} FROM `{table}` {condition}",
        "UPDATE `{table}` SET {columns} WHERE {condition}"
      ),
    ),
    "mysql" => array(),
  );

  private $connections = array();
  private $data = array();
  private $queries = array();
  private $results = array();
  private $current = 0;
  private $previous;

  //DB-related class functions.
  public function create(string $host = "localhost", string $db = "database", string $user = "username", string $pass = "password", string $engine = "mysql", string $custom_dsn = ""): int {
    $dsn = (empty($custom_dsn))? $this->get_dsn($host, $db, $engine) : $custom_dsn;

    try {
      $this->connections[] = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
      ]);
      $this->current = count($this->connections) - 1;
    } catch (PDOException $exception) {
      trigger_error("Error while attempting to create new connection!");
    }
    return $this->current;
  }

  public function test_connection(string $host, string $db, string $user, string $pass, string $engine = "mysql") {
    $dsn = (empty($custom_dsn)) ? $this->get_dsn($host, $db, $engine) : $custom_dsn;
    var_dump($dsn);
    try {
      $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false
      ]);
      echo "Connected!";
    } catch (PDOException $exception) {
      trigger_error("Error while attempting to create new connection!");
    }
  }

  public function query(string $query_str, bool $cache = false, bool $prepared = false, array $values = array()): int {
    $connection = $this->get_connection();
    if ($connection instanceof PDO) {
      $result = false;
      if ($prepared) {
        $statement = $connection->prepare($query_str);
        $result = $statement->execute($values);
      } else {
        $result = $connection->query($query_str);
      }

      if (!$result) {
        throw new Exception("Error occurred while executing query!");
      }

      if ($cache) {
        $this->queries[] = $result;
        return count($this->queries) - 1;
      }
      $this->previous = $result;
      return 0;
    }
    return -1;
  }

  public function create_table(string $name, array $columns, array $options = array()) {

  }

  public function drop_table(string $name) {

  }

  public function insert(string $table, array $columns, array $options = array()) {

  }

  public function select(array $table, array $columns, array $options = array()):int {

  }

  public function update(string $table, array $columns, array $options = array()) {

  }

  public function delete() {

  }

  //MISC Class Functions.
  public function close(int $id) {
    $this->connections[$id] = null;
  }

  public function change_connection(int $id) {
    $this->current = $id;
  }

  public function get_connection(): PDO {
    return $this->connections[$this->current];
  }

  public function store_data($data): int {
    $this->data = $data;
    return count($this->data) - 1;
  }

  public function get_data(int $id) {
    return $this->data[$id];
  }

  public function cache_result($result): int {
    $this->results[$result];
    return count($this->results) - 1;
  }

  public function get_result(int $id) {
    return $this->results[$id];
  }

  public function __construct() {

  }

  public function get_query(string $identifier, string $string = "", string $engine = "mysql"): string {
    if (!array_key_exists($engine, $this->pre_defined) || !array_key_exists($identifier, $this->pre_defined[$engine])
      || empty($string) && is_array($this->pre_defined[$engine][$identifier])
      || !empty($string) && !array_key_exists($string, $this->pre_defined[$engine][$identifier])
    ) {
      $engine = "default";
    }
    if (empty($string) && !is_array($this->pre_defined[$engine][$identifier])) {
      return $this->pre_defined[$engine][$identifier];
    } else if (empty($string) && is_array($this->pre_defined[$engine][$identifier])) {
      throw new Exception("Invalid query identifier & string combination!");
    } else {
      if (!empty($this->pre_defined[$engine][$identifier][$string])) {
        return $this->pre_defined[$engine][$identifier][$string];
      }
      throw new Exception("Invalid query identifier & string combination!");
    }
  }

  public function __destruct() {
    foreach ($this->connections as $connection) {
      $connection = null;
    }
  }

  //Private Utility functions
  private function get_dsn(string $host = "localhost", string $db = "database", string $engine = "mysql", array $extras = array()): string {
    $dsn = "";
    $to_replace = array("{host}", "{db}", "{engine}");
    $to_replace = array_merge($to_replace, array_keys($extras));
    $replacements = array($host, $db, $engine);
    $replacements = array_merge($replacements, array_values($extras));

    try {
      $dsn = $this->get_query("dsn", "", $engine);
      return str_ireplace($to_replace, $replacements, $dsn);
    } catch (Exception $e) {
      trigger_error("Exception while getting DSN string." . $e->getMessage());
    }
    return $dsn;
  }
}