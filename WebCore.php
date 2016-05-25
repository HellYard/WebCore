<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 5/10/2016
 * Time: 6:45 PM
 * Version: Beta 1
 * Last Modified: 5/10/2016 at 6:45 PM
 * Last Modified by Daniel Vidmar.
 */
class WebCore
{
    private static $instance;
    private static $controllers = array();

    private function __construct() {

    }

    public static function instance() {
        if(!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __clone() {
        trigger_error("Unable to clone WebCore registry class!");
    }

    public function get_controller($name) {
        if(is_object(self::$controllers[$name])) {
            return self::$controllers[$name];
        }
    }

    public function add_controller($name, $controller) {
        require_once('Controllers/'.$controller.'.php');
        self::$controllers[$name] = new $controller(self::$instance);
    }
}