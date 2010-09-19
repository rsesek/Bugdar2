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

require_once BUGDAR_ROOT . '/includes/model_bug.php';
require_once BUGDAR_ROOT . '/includes/model_comment.php';
require_once BUGDAR_ROOT . '/includes/search_engine.php';
require_once PHALANX_ROOT . '/views/custom_view_event.php';

// This creates a bug with some basic parameters.
class BugNewEvent extends \phalanx\events\Event implements \phalanx\views\CustomViewEvent
{
  public function action() { return 'insert'; }
  public function bug_reporter() { return Bugdar::$auth->current_user(); }

  static public function InputList()
  {
    return array();
  }

  static public function OutputList()
  {
    return array(
      'action',
      'bug_reporter'
    );
  }

  // Implement \phalanx\views\CustomViewEvent:
  public function CustomTemplateName()
  {
    return 'bug_view';
  }

  public function Fire()
  {
    Bugdar::$auth->RequireAuthentication();
  }
}
