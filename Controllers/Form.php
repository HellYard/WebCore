<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 6/14/2016
 * Time: 7:22 PM
 * Version: Beta 2
 * Last Modified: 6/14/2016 at 7:22 PM
 * Last Modified by Daniel Vidmar.
 */
class FormController {

  private $values = array();
  private $sets = array();
  private $elements = array();
  private $use_label = true;
  private $before = true;
  private $method = "POST";
  private $action = "#";
  private $form = "";

  private static $types = array(
    'label' => '<label for="%name%">%label%</label>',
    'input' => '<input type="%type%" value="%value%" name="%name%" />',
    'button' => '<button type="button">%value%</button>',
    'select' => '<select name="%name%">%options%</select>',
    'option' => '<option value="%name%">%value%</option>',
    'textarea' => '<textarea name="%name%" rows="%rows%" cols="%cols%">%value%</textarea>',
  );

  public function __construct() {

  }

  public function add_set($name, $order = 200) {
    if (!array_key_exists($name, $this->sets)) {
      if ($order >= count($this->sets)) { $order = count($this->sets) - 1; }
      array_splice($this->sets, $order, 0, $name);
    }
  }

  public function remove_set($name) {
    if (array_key_exists($name, $this->sets)) {
      array_splice($this->sets, array_search($name, array_keys($this->sets)), 1);
    }
  }

  public function add_element($name, $options = array(), $order = 200) {
    if (!array_key_exists($name, $this->elements)) {
      if ($order >= count($this->elements)) { $order = count($this->elements) - 1; }
      array_splice($this->elements, $order, 0, $name);
      $this->elements[$name] = $options;
    }
  }

  public function remove_element($name) {
    if (array_key_exists($name, $this->elements)) {
      array_splice($this->elements, array_search($name, array_keys($this->elements)), 1);
    }
  }

  public function set_method($method) {
    $this->method = $method;
  }

  public function set_action($url) {
    $this->action = $url;
  }

  public function set_label($label) {
    $this->use_label = $label;
  }

  public function parse($submitted) {
    foreach ($submitted as $key => $value) {
      $this->values[$key] = htmlspecialchars($value);
    }

    foreach ($this->elements as $key => $value) {
      if (isset($value["required"]) && $value["required"]) {
        if (!in_array($key, $this->values)) {
          throw new Exception("Required form parameter is missed");
        }
      }
    }
  }

  public function build() {
    $this->form .= '<form action="'.$this->action.'" method="'.$this->method.'">';
    $this->form .= $this->build_sets();
    $this->form .= '</form>';
  }

  private function build_sets() {
    $sets = "";
    $sets .= $this->build_set("default");
    foreach (array_keys($this->sets) as $set) {
      $sets .= $this->build_set($set);
    }
    return $sets;
  }

  private function build_set($name) {
    $set = "<fieldset>";
    $set .= $this->build_elements($name);
    $set .= "</fieldset>";
    return $set;
  }

  private function build_elements($set) {
    $elements = "";
    foreach ($this->elements as $key => $value) {
      if (!is_array($value)) {
        throw new UnexpectedValueException("Element options should be an array.");
      }

      if ($set == "default" && !array_key_exists("set", $value) || $value["set"] == $set) {
        $replace = array("%name%", "%value%", "%type%", "%cols%", "%rows%", "%options%");
        $replacements = array("%name%", "%value%", "%type%", "%cols%", "%rows%", "%options%");

        $label = (array_key_exists("label", $value)) ? $value["label"] : $key." Label";
        if ($this->use_label && $this->before) {
          $elements .= str_ireplace(array("%name%", "%label%",), array($key, $label), self::$types["label"]);
        }

        switch ($value["type"]) {
          case "textarea":
            if (array_key_exists("name", $value)) { $replacements[0] = $value["name"]; }
            if (array_key_exists("value", $value)) { $replacements[1] = $value["value"]; }
            if (array_key_exists("rows", $value)) { $replacements[4] = $value["rows"]; }
            if (array_key_exists("cols", $value)) { $replacements[3] = $value["cols"]; }
            $elements .= str_ireplace($replace, $replacements, self::$types["textarea"]);
            break;
          case "select":
            if (array_key_exists("name", $value)) { $replacements[0] = $value["name"]; }
            if (array_key_exists("value", $value)) { $replacements[1] = $value["value"]; }
            $replacements[5] = $this->build_options($key, $value["options"]);
            $elements .= str_ireplace($replace, $replacements, self::$types["select"]);
            break;
          default:
            $type = ($value["type"] == "input" || $value["type"] == "button") ? $value["type"] : "input";

            if (array_key_exists("name", $value)) { $replacements[0] = $value["name"]; }
            if (array_key_exists("value", $value)) { $replacements[1] = $value["value"]; }
            $replacements[2] = $type;

            $elements .= str_ireplace($replace, $replacements, self::$types[$type]);
            break;

        }

        if ($this->use_label && !$this->before) {
          $elements .= str_ireplace(array("%name%", "%label%",), array($key, $label), self::$types["label"]);
        }
      }
    }
    return $elements;
  }

  private function build_options($select, $element) {
    $replace = array("%name%", "%value%");
    $replacements = array();
    $options = "";
    $i = 0;

    foreach ($element as $e) {
      $name = (array_key_exists("name", $e)) ? $e["name"] : $select."_option_".$i;
      $value = (array_key_exists("value", $e)) ? $e["value"] : "Option ".$i;
      $replacements[] = $name;
      $replacements[] = $value;
      $options .= str_ireplace($replace, $replacements, self::$types["option"]);
      $i++;
    }

    return $options;
  }
}