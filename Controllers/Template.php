<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 5/25/2016
 * Time: 10:05 AM
 * Version: Beta 2
 * Last Modified: 5/25/2016 at 10:05 AM
 * Last Modified by Daniel Vidmar.
 */
class TemplateController implements Controller {

  /*
   * TODO/GOALS:
   *  ` - done
   *  * - WIP
   * Easy-to-use yet powerful templating engine in one class.
   * Ability to create template "snippets". `
   * Caching system `
   *   support for multiple caches `
   * variable replacement `
   * include multiple files
   * Documentation
   *
   * TODO/FUTURE:
   * Ability to allow templating of CSS files.
   */
  private $variables = array();
  private $snippets = array();
  private $location;

  public function __construct(string $cache_location = "") {
    $this->location = $cache_location;
  }

  //Useful class functions.
  public function template(string $name, bool $cache = true, int $time = 6000): string {
    $result = "";
    if($cache && $this->is_cached($name)) {
      $result = $this->read_cache($name);
    } else {
      $result = $this->parse_template($name);
      if($cache) {
        $this->cache($name, $result, $time);
      }
    }
    return $result;
  }

  public function snip(string $name, bool $override = false) {
    if(!array_key_exists($name, $this->snippets) || array_key_exists($name, $this->snippets) && !$this->snippets[$name]["ob"]) {
      if(array_key_exists($name, $this->snippets) && $override) {
        ob_start();
        $this->snippets[$name] = array(
          "ob" => true,
          "data" => ""
        );
      }
    } else {
      $this->snippets[$name]["data"] = ob_get_flush();
    }
  }

  //Public utility functions
  public function cache(string $name, string $data, int $time) {
    $file = $this->location.$name."wcc";
    $current_time = time();
    $info = array(
      "timestamp" => $current_time,
      "expires" => $current_time + $time,
      "data" => $data
    );

    $contents = implode(PHP_EOL, $info);

    $gz_contents = gzencode($contents);

    $handler = fopen($file, 'w+');
    fwrite($handler, $gz_contents);
    fclose($handler);
  }

  private function is_cached(string $name): bool {
    $file = $this->location.$name."wcc";

    if(file_exists($file)) {
      $data = $this->read_cache($name);

      return $data["expires"] < time();
    }

    return false;
  }

  public function add_variable(string $name, $value) {
    $this->variables[$name] = $value;
  }

  public function remove_variable(string $name) {
    if(in_array($name, $this->variables)) {
      $this->variables[$name] = null;
    }
  }

  //Private utility functions.
  private function parse_template(string $name): string {
    $result = "";
    //TODO: Create this function.
    return $result;
  }

  private function read_cache(string $name): string {
    $result = array(
      "timestamp" => "",
      "expires" => "",
      "data" => ""
    );
    $file = $this->location.$name."wcc";

    if(file_exists($file)) {

      $lines = explode(PHP_EOL, gzdecode(file_get_contents($file)));

      $result["timestamp"] = $lines[0];
      $result["expires"] = $lines[1];
      $result["data"] = $lines[2];

      return $result;
    }
    throw new Exception("Error while attempting to locate the specified cache file!");
  }

  //TODO: Possibly replace with regex.
  private function replace_variables(string $string): string {
    foreach($this->variables as $key => $value) {
      if(!empty($value)) {
        $string = str_ireplace($key, $value, $string);
      }
    }
    return $string;
  }
}