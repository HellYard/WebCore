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
class DataController {

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
        "create" => "CREATE TABLE IF NOT EXISTS `{name}` (" .
          "{columns}" .
          ");",
        "DROP" => "DROP TABLE `{name}`;"
      ),
    ),
    "sybase" => array(
      "dsn" => "dblib:dbname={db};host={host}",
    ),
    "firebird" => array(
      "dsn" => "firebird:dbname={host}:{db}",
    ),
    "ibm" => array(
      "dsn" => "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={db};HOSTNAME={host};PROTOCOL=TCPIP;",
    ),
    "informix" => array(
      "dsn" => "informix:host={host};database={db};protocol=onsoctcp;EnableScrollableCursors=1",
    ),
    "ms" => array(
      "dsn" => "sqlsrv:Server={host};Database={db}",
    ),
    "azure" => array(
      "dsn" => "sqlsrv:Server={host}.database.windows.net;Database={db}",
    ),
    "oracle" => array(
      "dsn" => "oci:dbname=//{host}/{db}",
    ),
    "db2" => array(
      "dsn" => "odbc:DRIVER={IBM DB2 ODBC DRIVER};DATABASE={db};HOSTNAME={host};PROTOCOL=TCPIP;",
    ),
    "access" => array(
      "dsn" => "odbc:Driver={Microsoft Access Driver (*.mdb)};Dbq={db};",
    ),
    "postgre" => array(
      "dsn" => "pgsql:host={host};dbname={db};",
    ),
    "sqlite" => array(
      "dsn" => "sqlite:{db}",
    ),
    "sqlite2" => array(
      "dsn" => "sqlite2:{db}",
    ),
    "4d" => array(
      "dsn" => "host={host}",
    ),
    "cubrid" => array(),
    "mysql" => array(),
  );

  private $connections = array();
  private $data = array();
  private $queries = array();
  private $results = array();
  private $current = 0;
  private $previous;

  //DB-related class functions.
  public function create($host = "localhost", $db = "database", $user = "username", $pass = "password", $engine = "mysql", $custom_dsn = "") {
    $dsn = (empty($custom_dsn)) ? $this->get_dsn($host, $db, $engine) : $custom_dsn;

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

  public function query($query_str, $cache = false, $prepared = false, $values = array()) {
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



  //MISC Class Functions.
  public function close($id) {
    $this->connections[$id] = null;
  }

  public function change_connection($id) {
    $this->current = $id;
  }

  public function get_connection() {
    return $this->connections[$this->current];
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
    foreach ($this->connections as $connection) {
      $connection = null;
    }
  }

  //Private Utility functions
  private function get_dsn($host = "localhost", $db = "database", $engine = "mysql") {
    $dsn = "";
    $to_replace = array("{host}", "{db}", "{engine}");
    $replacements = array($host, $db, $engine);

    try {
      $dsn = $this->get_query("dsn", "", $engine);
      str_ireplace($to_replace, $replacements, $dsn);
    } catch (Exception $e) {
      trigger_error("Exception while getting DSN string." . $e->getMessage());
    }
    return $dsn;
  }

  private function get_query($identifier, $string = "", $engine = "mysql") {
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
}