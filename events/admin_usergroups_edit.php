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

require_once BUGDAR_ROOT . '/includes/model_usergroup.php';

// Edits an individual usergroup.
class AdminUsergroupsEditEvent extends phalanx\events\Event
{
  protected $usergroup = array();
  public function usergroup() { return $this->usergroup; }

  static public function InputList()
  {
    return array(
      '_id',
      'title',
      'display_title',
      'permissions'
    );
  }

  static public function OutputList()
  {
    return array('usergroup');
  }

  public function WillFire()
  {
    Bugdar::$auth->RequireAuthentication();
    // TODO: check admin permission
  }

  public function Fire()
  {
    // If an ID was passed, try updating the record.
    if ($this->input->_id) {
      try {
        $this->usergroup = new Usergroup($this->input->_id);
        $this->usergroup->FetchInto();
      } catch (\phalanx\data\ModelException $e) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('ERROR_INVALID_ID')));
        return;
      }
    } else {
      // Otherwise, create a new one.
      $this->usergroup = new Usergroup();
    }

    if ($this->input->_method == 'POST') {
      $title = \phalanx\data\Cleaner::HTML($this->input->title);
      if (empty($title)) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent('The title field is required.'));
        return;
      }
      $this->usergroup->title = $title;

      if (!empty($this->input->display_title)) {
        $this->usergroup->display_title = \phalanx\data\Cleaner::HTML($this->input->display_title);
      }

      $mask = 0;
      foreach ($this->input->permissions as $name => $bit) {
        $mask += $bit * Usergroup::$permissions[$name];
      }
      $this->usergroup->mask = $mask;

      // Save the actual record.
      if ($this->input->_id) {
        $this->usergroup->Update();
      } else {
        $this->usergroup->Insert();
      }
    }
  }
}
