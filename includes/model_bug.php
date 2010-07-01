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

    // Cached attributes.
    protected $attributes = array();

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
        if ($this->attributes)
            return $this->attributes;

        $stmt = Bugdar::$db->Prepare("SELECT * from " . TABLE_PREFIX . "bug_attributes WHERE bug_id = ?");
        $stmt->Execute(array($this->bug_id));
        while ($attr = $stmt->FetchObject())
            $this->attributes[] = $attr;
        return $this->attributes;
    }

    // Returns the user who reported the bug.
    public function FetchReporter()
    {
        $user = new User($this->reporting_user_id);
        $user->FetchInto();
        return $user;
    }

    // Sets an attribute. If |key| is NULL, this will act as a tag. Note that
    // this does not perform validation or permission checks.
    public function SetAttribute($key, $value)
    {
        $this->FetchAttributes();
        $stmt = NULL;
        foreach ($this->attributes as $i => $attr) {
            if ($attr->attribute_title == $key) {
                $stmt = Bugdar::$db->Prepare("
                    UPDATE " . TABLE_PREFIX . "bug_attributes
                    SET value = :value
                    WHERE bug_id = :bug_id
                    AND attribute_title = :attribute_title
                ");
                $this->attributes[$i]->value = $value;
                break;
            }
        }
        if (!$stmt) {
            $stmt = Bugdar::$db->Prepare("
                INSERT INTO " . TABLE_PREFIX . "bug_attributes
                    (bug_id, attribute_title, value)
                VALUES
                    (:bug_id, :attribute_title, :value)
            ");
        }
        $stmt->Execute(array(
            'bug_id'          => $this->bug_id,
            'attribute_title' => $key,
            'value'           => $value
        ));
    }

    // Removes an attribute or tag.
    public function RemoveAttribute($key, $is_tag = FALSE)
    {
        if ($is_tag) {
            $stmt = Bugdar::$db->Prepare("
                DELETE FROM " . TABLE_PREFIX . "bug_attributes
                WHERE bug_id = ?
                AND value = ?
                AND attribute_title IS EMPTY
            ");
            $stmt->Execute(array($this->bug_id, $key));
        } else {
            $stmt = Bugdar::$db->Prepare("
                DELETE FROM " . TABLE_PREFIX . "bug_attributes
                WHERE bug_id = ?
                AND attribute_title = ?
            ");
            $stmt->Execute(array($this->bug_id, $key));
        }
    }
}
