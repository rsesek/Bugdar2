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

// This generates a list of bugs.
class BugListEvent extends phalanx\events\Event
{
  // The list of bugs we compiled.
  protected $bugs = array();
  public function bugs() { return $this->bugs; }

  static public function InputList()
  {
    // Nothing now. Future work will be search params, filters, view layout
    // info, etc.
    return array();
  }

  static public function OutputList()
  {
    return array('bugs');
  }

  public function Fire()
  {
    $query = Bugdar::$db->Query("
      SELECT bugs.*, users.alias as reporting_alias
      FROM " . TABLE_PREFIX . "bugs bugs
      LEFT JOIN " . TABLE_PREFIX . "users users
        ON (bugs.reporting_user_id = users.user_id)
      ORDER BY reporting_date
      LIMIT 30
    ");
    while ($bug = $query->FetchObject())
      $this->bugs[] = $bug;
  }
}
