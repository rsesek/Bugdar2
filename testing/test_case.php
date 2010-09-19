<?php
// Bugdar 2
// Copyright (c) 2010 Blue Static
// 
// This program is free software: you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the Free
// Software Foundation, either version 3 of the License, or any later version.
// 
// This program is distributed in the hope that it will be useful, but WITHOUT
// ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
// FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
// more details.
//
// You should have received a copy of the GNU General Public License along with
// this program.  If not, see <http://www.gnu.org/licenses/>.

require BUGDAR_ROOT . '/includes/model_user.php';
require BUGDAR_ROOT . '/includes/auth/auth_local.php';
require_once 'PHPUnit/Framework.php';

class AuthenticationTest extends Authentication
{
  public function IsLoggedIn()
  {
    return ($this->current_user != NULL);
  }

  public function __construct($user)
  {
    $this->current_user = $user;
  }

  protected function _PerformLogin()
  {
    throw new Exception('Should not have _PerformLogin() called in ' . __CLASS__);
  }

  protected function _PerformLogout()
  {}
}

// This is a specialization of the generic test case that has Bugdar-specific
// features.
abstract class BugdarTestCase extends PHPUnit_Framework_TestCase
{
  public function setUp()
  {
    phalanx\events\EventPump::set_pump(new \phalanx\events\EventPump());
    phalanx\events\EventPump::Pump()->set_output_handler(
        new phalanx\events\UnitTestOutputHandler());
    Bugdar::$auth = NULL;
  }

  // Creates a mock authentication instance and test user.
  protected function _RequireAuthentication()
  {
    $user = new User();
    $user->email   = 'bugdar@bluestatic.org';
    $user->Insert();
    Bugdar::$auth = new AuthenticationTest($user);
  }
}
