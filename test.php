<?php
/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 6/19/2016
 * Time: 2:04 PM
 * Version: Beta 2
 * Last Modified: 6/19/2016 at 2:04 PM
 * Last Modified by Daniel Vidmar.
 */

require_once('WebCore.php');
$app = WebCore::instance();
$app->get("db")->test_connection("localhost", "test", "root", "", "mysql");