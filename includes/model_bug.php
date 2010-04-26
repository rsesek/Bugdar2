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

require_once PHALANX_ROOT . '/data/model.php';

class Bug extends phalanx\data\Model
{
    // Model properties.
    protected $table_prefix = TABLE_PREFIX;
    protected $table = 'bugs';
    protected $primary_key = 'bug_id';
    protected $condition = 'bug_id = :bug_id';

    // Struct properties.
    protected $fields = array(
        'bug_id',
        'title',
        'reporting_user_id',
        'reporting_date',
        'hidden',
        'first_comment_id'
    );

    // Fetches all the comments for a bug and returns them in a time descending
    // order, oldest to newest.
    public function FetchComments()
    {
        $comments = array();
        $stmt     = Bugdar::$db->Prepare("
            SELECT comments.*, users.alias as post_alias
            FROM " . TABLE_PREFIX . "comments comments
            LEFT JOIN " . TABLE_PREFIX . "users users
                ON (comments.post_user_id = users.user_id)
            WHERE comments.bug_id = ?
            ORDER BY comments.post_date
        ");
        $stmt->Execute(array($this->bug_id));
        while ($comment = $stmt->FetchObject())
            $comments[] = $comment;
        return $comments;
    }

    // Returns an array of all the attributes the bug has.
    public function FetchAttributes()
    {
        $stmt = Bugdar::$db->Prepare("SELECT * from " . TABLE_PREFIX . "bug_attributes WHERE bug_id = ?");
        $stmt->Execute(array($this->bug_id));
        $attributes = array();
        while ($attr = $stmt->FetchObject())
            $attributes[] = $attr;
        return $attributes;
    }

    // Returns the user who reported the bug.
    public function FetchReporter()
    {
        // BUG:K003 : Bug::FetchReporter() should return a User Model object
        $stmt = Bugdar::$db->Prepare("SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id = ?");
        $stmt->Execute(array($this->reporting_user_id));
        return $stmt->FetchObject();
    }
}
