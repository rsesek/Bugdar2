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

use phalanx\events\EventPump as EventPump;

require_once BUGDAR_ROOT . '/events/user_login.php';

class UserLoginEventTest extends BugdarTestCase
{
  const EMAIL = 'login-test@bugdar.bluestatic.org';

  public function testAlreadyLoggedIn()
  {
    $this->_RequireAuthentication();
    $event = new UserLoginEvent();
    EventPump::Pump()->PostEvent($event);
    $this->assertTrue($event->is_cancelled());
  }

  public function testUserLogin()
  {
    Bugdar::$auth = new AuthenticationTest(NULL);

    // Create the user.
    $user = new User();
    $user->email    = self::EMAIL;
    $user->alias    = 'Robert';
    $user->password = sha1('moo');
    $user->Insert();

    $data = new phalanx\base\PropertyBag(array(
      'do'       => 'fire',
      'email'    => self::EMAIL,
      'password' => 'moo'
    ));
    $event = $this->getMock('UserLoginEvent', array('_SetCookie'), array($data));
    $event->expects($this->exactly(2))->method('_SetCookie');
    EventPump::Pump()->PostEvent($event);
    $this->assertTrue($event->was_successful());
  }

  public function testBadPassword()
  {
    Bugdar::$auth = new AuthenticationTest(NULL);

    $data = new phalanx\base\PropertyBag(array(
      'do'       => 'fire',
      'email'    => self::EMAIL,
      'password' => 'foo'
    ));
    $event = new UserLoginEvent($data);
    $self  = &$this;
    EventPump::Pump()->PostEvent($event);
    $this->assertFalse($event->was_successful());
  }
}
