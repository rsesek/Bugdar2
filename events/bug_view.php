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

require_once BUGDAR_ROOT . '/events/standard_error.php';
require_once BUGDAR_ROOT . '/includes/model_bug.php';
require_once BUGDAR_ROOT . '/includes/model_user.php';
require_once BUGDAR_ROOT . '/includes/model_usergroup.php';

// This views information of a bug.
class BugViewEvent extends phalanx\events\Event
{
    // The bug object.
    protected $bug = NULL;
    public function bug() { return $this->bug; }

    // The user who submitted the bug object.
    protected $bug_reporter = NULL;
    public function bug_reporter() { return $this->bug_reporter; }

    // The attributes the bug has.
    protected $attributes = array();
    public function attributes() { return $this->attributes; }

    // Array of comments. Oldest to newest.
    protected $comments = array();
    public function comments() { return $this->comments; }

    static public function InputList()
    {
        return array('_id');
    }

    static public function OutputList()
    {
        return array(
            'bug',
            'comments',
            'bug_reporter',
            'attributes'
        );
    }

    public function WillFire()
    {
        $user = Bugdar::$auth->current_user();
        if (($user && !$user->CheckGroupPermission(Usergroup::CAN_VIEW)) ||
            (!$user && !Usergroup::AnonymousGroup()->mask & Usergroup::CAN_VIEW)) {
            EventPump::Pump()->RaiseEvent(new StandardErrorEvent('NO_PERMISSION_CAN_VIEW'));
            return;
        }
    }

    public function Fire()
    {
        $bug = new Bug($this->input->_id);
        $bug->FetchInto();
        $this->bug          = $bug;
        $this->bug_reporter = $bug->FetchReporter();
        $this->attributes   = $bug->FetchAttributes();
        $this->comments     = $bug->FetchComments();
    }
}
