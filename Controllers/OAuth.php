<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 6/5/2016
 * Time: 6:16 PM
 * Version: Beta 2
 * Last Modified: 6/5/2016 at 6:16 PM
 * Last Modified by Daniel Vidmar.
 */
class OAuthController {

  /*
   * TODO/GOALS:
   *  ` - done
   *  * - WIP
   *
   * Ability to setup an OAuth provider with ease.
   * Ability to connect with an OAuth service with ease.
   */
  public function __construct() {

  }

  public function generate_consumer() {

  }
}

class WebProvider {
  private $error = false;
  private $provider;

  public function __construct() {
    try {
      $provider = new OAuthProvider();
    } catch (OAuthException $e) {
      trigger_error(OAuthProvider::reportProblem($e));
    }
  }

  public function oauth($action = null) {
    if ($this->error || empty($action)) return;

    switch ($action) {
      case "":
        break;
    }
  }

  private function consumer_lookup($provider) {

  }

  private function token_handler() {

  }

  private function nonce_checker() {

  }
}