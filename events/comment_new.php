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

// This event creates a new comment on a bug.
class CommentNewEvent extends phalanx\events\Event
{
    protected $user_id = 0;
    public function user_id() { return $this->user_id; }

    static public function InputList()
    {
        return array(
            'do',
            'bug_id',
            'body',
        );
    }

    static public function OutputList()
    {
        return array('comment_id');
    }

    public function WillFire()
    {
        Bugdar::$auth->RequireAuthentication();
    }

    public function Fire()
    {
        if ($this->input->do == 'submit')
        {
            $bug_id = phalanx\input\Cleaner::Int($this->input->bug_id);
            $user   = Bugdar::$auth->current_user();

            $stmt = Bugdar::$db->Prepare("SELECT * FROM " . TABLE_PREFIX . "bugs WHERE bug_id = ?");
            $stmt->Execute(array($bug_id));
            if (!$stmt->FetchObject())
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_ID_NOT_FOUND')));

            $body = trim($this->input->body);
            if (empty($body))
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('COMMENT_MISSING_BODY')));

            $stmt = Bugdar::$db->Prepare("
                INSERT INTO " . TABLE_PREFIX ."comments
                    (bug_id, post_user_id, post_date, body)
                VALUES
                    (?, ?, ?, ?)
            ");
            $stmt->Execute(array($bug_id, $user->user_id, time(), $body));
            $this->comment_id = Bugdar::$db->LastInsertID();

            EventPump::Pump()->PostEvent(new StandardSuccessEvent('view_bug/' . $bug_id, l10n::S('USER_REGISTER_SUCCESS')));
        }
    }
}
