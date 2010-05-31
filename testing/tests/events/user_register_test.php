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

require_once BUGDAR_ROOT . '/events/user_register.php';

class UserRegisterEventTest extends BugdarTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testAlreadyRegistered()
    {
        $this->_RequireAuthentication();
        $event = new UserRegisterEvent();
        EventPump::Pump()->PostEvent($event);
        $this->assertTrue($event->is_cancelled());
    }

    public function testUserRegister()
    {
        Bugdar::$auth = new AuthenticationTest(NULL);
        $data = new phalanx\base\PropertyBag(array(
            'do'           => 'submit',
            'email'        => 'robert@bluestatic.org',
            'alias'        => 'Robert',
            'password'     => 'abc123'
        ));
        $event = new UserRegisterEvent($data);
        EventPump::Pump()->PostEvent($event);

        $user = new User($event->user_id());
        $user->FetchInto();
        $this->assertEquals($data->email, $user->email);
        $this->assertEquals($data->alias, $user->alias);
        $this->assertGreaterThanOrEqual(5, strlen($user->salt));
        $this->assertEquals(md5(sha1($data->password) . $user->salt), $user->password);
        $this->assertGreaterThanOrEqual(5, strlen($user->authkey));
    }

    public function testDuplicateEmail()
    {
        Bugdar::$auth = new AuthenticationTest(NULL);
        $data = new phalanx\base\PropertyBag(array(
            'do'           => 'submit',
            'email'        => 'robert@bluestatic.org',
            'alias'        => 'Robert',
            'password'     => 'abc123'
        ));
        $event = new UserRegisterEvent($data);
        EventPump::Pump()->PostEvent($event);

        $last_event = EventPump::Pump()->GetEventChain()->Top();
        $this->assertType('StandardErrorEvent', $last_event);
    }
}
