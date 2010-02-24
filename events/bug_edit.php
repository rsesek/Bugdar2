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

require_once BUGDAR_ROOT . '/includes/model_comment.php';
require_once BUGDAR_ROOT . '/includes/search_engine.php';

// This event creates a new comment on a bug.
class BugEditEvent extends phalanx\events\Event
{
    static public function InputList()
    {
        return array(
            'bug_id',
            'comment_body',
            'attributes'
        );
    }

    static public function OutputList()
    {
        return array();
    }

    public function WillFire()
    {
        Bugdar::$auth->RequireAuthentication();
    }

    public function Fire()
    {
        $bug_id = phalanx\data\Cleaner::Int($this->input->bug_id);
        $user   = Bugdar::$auth->current_user();

        $stmt = Bugdar::$db->Prepare("SELECT * FROM " . TABLE_PREFIX . "bugs WHERE bug_id = ?");
        $stmt->Execute(array($bug_id));
        $bug = $stmt->FetchObject();
        if (!$bug)
            EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_ID_NOT_FOUND')));

        Bugdar::$db->BeginTransaction();

        $body = trim($this->input->comment_body);
        if (!empty($body))
        {
            $comment = new Comment();
            $comment->bug_id       = $bug_id;
            $comment->post_user_id = $user->user_id;
            $comment->post_date    = time();
            $comment->body         = $body;
            $comment->Insert();
        }

        // Delete existing attributes.
        $stmt = Bugdar::$db->Prepare("DELETE FROM " . TABLE_PREFIX . "bug_attributes WHERE bug_id = ?");
        $stmt->Execute(array($bug_id));
        
        // Replace the attributes with the new ones.
        foreach ($this->input->attributes as $attr)
        {
            // Skip completely empty attributes.
            if (empty($attr['title']) && empty($attr['value']))
                continue;

            // Attributes without titles are tags.
            if (empty($attr['title']))
            {
                $stmt = Bugdar::$db->Prepare("INSERT INTO " . TABLE_PREFIX . "bug_attributes (bug_id, value) VALUES (?, ?)");
                $stmt->Execute(array($bug_id, $attr['value']));
            }
            // Attributes with both title and value are fields.
            else
            {
                $stmt = Bugdar::$db->Prepare("INSERT INTO " . TABLE_PREFIX . "bug_attributes (bug_id, attribute_title, value) VALUES (?, ?, ?)");
                $stmt->Execute(array($bug_id, $attr['title'], $attr['value']));
            }
        }

        Bugdar::$db->Commit();

        $search = new SearchEngine();
        $search->IndexBug($bug);

        EventPump::Pump()->PostEvent(new StandardSuccessEvent('view_bug/' . $bug_id, l10n::S('USER_REGISTER_SUCCESS')));
    }
}
