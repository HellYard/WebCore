<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 6/5/2016
 * Time: 5:35 PM
 * Version: Beta 2
 * Last Modified: 6/5/2016 at 5:35 PM
 * Last Modified by Daniel Vidmar.
 */
class AuthenticationController {

  /*
   * TODO/GOALS:
   *  ` - done
   *  * - WIP
   * Ability to create, modify, and delete User Groups with ease.
   * Ability to create, modify, and delete User Profiles.
   * Ability to check whether or not a user is online.
   * Ability to lock pages to certain users, user groups, and with permission nodes.
   * Ability to modify, add, and delete permission nodes.
   * Ability to attach permission nodes to certain actions.
   * Ability to allow a user to login with a username and/or email address.
   */
  public function __construct() {

  }

}

class Group {

  private $permissions = array();
  private $id = null;
  private $name = null;
  private $admin = false;
  private $preset = false;

}

class User {

  private $permissions = array();
  private $id = null;
  private $ip = null;
  private $avatar = "";
  private $name = null;
  private $password = null;
  private $group = null;
  public $email = null;
  public $registered = null;
  public $logged_in = null;
  public $activation_key = null;
  public $activated = 0;
  public $banned = 0;
  public $online = 0;

}