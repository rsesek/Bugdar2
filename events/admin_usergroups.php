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

// Queries for the list of usergroups.
class AdminUsergroupsEvent extends phalanx\events\Event
{
  protected $usergroups = array();
  public function usergroups() { return $this->usergroups; }

  static public function InputList()
  {
    return array();
  }

  static public function OutputList()
  {
    return array('usergroups');
  }

  public function WillFire()
  {
    Bugdar::$auth->RequireAuthentication();
    // TODO: check admin permission
  }

  public function Fire()
  {
    $this->usergroups = Usergroup::FetchGroup();
  }
}
