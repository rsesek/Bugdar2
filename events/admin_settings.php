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

// This event allows viewing of a user profile.
class AdminSettingsEvent extends phalanx\events\Event
{
  protected $settings = array();
  public function settings() { return $this->settings; }

  static public function InputList()
  {
    return array('settings');
  }

  static public function OutputList()
  {
    return array('settings');
  }

  public function WillFire()
  {
    Bugdar::$auth->RequireAuthentication();
    // TODO: check admin permission
  }

  public function Fire()
  {
    $valid_settings = array(
      // The absolute path to the Bugdar installation.  Used to construct links.
      'webroot',

      // The name of the bug tracker.
      'tracker_name',
    );

    // Load the current settings.
    $this->settings = Bugdar::$settings;

    if ($this->input->_method == 'POST') {
      // Create the prepared statement that we reuse for each setting.
      $query = Bugdar::$db->Prepare("
        INSERT INTO " . TABLE_PREFIX . "settings
          (setting, value)
        VALUES
          (:setting, :value)
        ON DUPLICATE KEY UPDATE value = :value
      ");

      // Update all the settings atomically.
      Bugdar::$db->BeginTransaction();
      foreach ($valid_settings as $setting) {
        if (!isset($this->input->settings[$setting]))
          continue;
        $value = $this->input->settings[$setting];
        $query->Execute(array(
          'setting' => $setting,
          'value' => $value
        ));
        $this->settings[$setting] = $value;
      }
      Bugdar::$settings = $this->settings;
      Bugdar::$db->Commit();
    }
  }
}
