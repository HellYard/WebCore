<?php

/**
 * Created by Daniel Vidmar.
 * User: Daniel
 * Date: 6/21/2016
 * Time: 7:46 PM
 * Version: Beta 2
 * Last Modified: 6/21/2016 at 7:46 PM
 * Last Modified by Daniel Vidmar.
 */
class User {

  private $permissions = array();
  private $id = null;
  private $ip = null;
  private $avatar = "";
  private $name = null;
  private $password = null;
  private $group = null;
  private $email = null;
  private $registered = null;
  private $logged_in = null;
  private $activation_key = null;
  private $activated = 0;
  private $banned = 0;
  private $online = 0;

}