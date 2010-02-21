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

// This views information of a bug.
class BugViewEvent extends phalanx\events\Event
{
    // The full bug object.
    protected $bug = NULL;
    public function bug() { return $this->bug; }

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
            'comments'
        );
    }

    public function Fire()
    {
        $stmt = Bugdar::$db->Prepare("
            SELECT bugs.*, users.alias as reporting_alias
            FROM " . TABLE_PREFIX . "bugs bugs
            LEFT JOIN " . TABLE_PREFIX . "users users
                ON (bugs.reporting_user_id = users.user_id)
            WHERE bugs.bug_id = ?
        ");
        $stmt->Execute(array($this->input->_id));
        $this->bug = $stmt->FetchObject();

        $stmt = Bugdar::$db->Prepare("SELECT * from " . TABLE_PREFIX . "bug_attributes WHERE bug_id = ?");
        $stmt->Execute(array($this->input->_id));
        $this->bug->attributes = array();
        while ($attr = $stmt->FetchObject())
            $this->bug->attributes[] = $attr;

        $stmt = Bugdar::$db->Prepare("
            SELECT comments.*, users.alias as post_alias
            FROM " . TABLE_PREFIX . "comments comments
            LEFT JOIN " . TABLE_PREFIX . "users users
                ON (comments.post_user_id = users.user_id)
            WHERE comments.bug_id = ?
            ORDER BY comments.post_date
        ");
        $stmt->Execute(array($this->input->_id));
        while ($comment = $stmt->FetchObject())
            $this->comments[] = $comment;
    }
}
