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

require_once BUGDAR_ROOT . '/events/admin_settings.php';

class AdminSettingsEventTest extends BugdarTestCase
{
  public function setUp()
  {
    parent::setUp();
    Bugdar::$settings = array();
    $this->_RequireAuthentication();
  }

  public function testReadingSettings()
  {
    Bugdar::$settings['foo'] = 'bar';
    $event = new AdminSettingsEvent();
    EventPump::Pump()->PostEvent($event);
    $settings = $event->settings();
    $this->assertEquals('bar', $settings['foo']);
  }

  public function testSavingValidSetting()
  {
    $data = new \phalanx\base\PropertyBag(array(
      'settings' => array('webroot' => '/bugdar2/'),
      '_method' => 'POST'
    ));
    $event = new AdminSettingsEvent($data);
    EventPump::Pump()->PostEvent($event);
    $settings = $event->settings();
    $this->assertEquals('/bugdar2/', $settings['webroot']);
    $row = Bugdar::$db->Query("SELECT * FROM settings WHERE setting = 'webroot'")->FetchObject();
    $this->assertEquals('/bugdar2/', $row->value);
  }

  public function testSavingInvalidSetting()
  {
    $data = new \phalanx\base\PropertyBag(array(
      'settings' => array(
        'webroot'    => '/bugdar2/',
        'badsetting' => 'test'
      ),
      '_method' => 'POST'
    ));
    $event = new AdminSettingsEvent($data);
    EventPump::Pump()->PostEvent($event);

    $settings = $event->settings();
    $this->assertEquals('/bugdar2/', $settings['webroot']);
    $this->assertNull($settings['badsetting']);

    $row = Bugdar::$db->Query("SELECT * FROM settings WHERE setting = 'webroot'")->FetchObject();
    $this->assertEquals('/bugdar2/', $row->value);

    $row = Bugdar::$db->Query("SELECT * FROM settings WHERE setting = 'badsetting'")->FetchObject();
    $this->assertNull($row->value);
  }
}
