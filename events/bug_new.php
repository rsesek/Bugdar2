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

// This creates a bug with some basic parameters.
class BugNewEvent extends phalanx\events\Event
{
    protected $bug_id = 0;
    public function bug_id() { return $this->bug_id; }

    protected $comment_id = 0;
    public function comment_id() { return $this->comment_id; }

    static public function InputList()
    {
        return array(
            'do',
            'title',
            'comment_body'
        );
    }

    static public function OutputList()
    {
        return array(
            'bug_id',
            'comment_id'
        );
    }

    public function WillFire()
    {
        Bugdar::$auth->RequireAuthentication();
    }

    public function Fire()
    {
        if ($this->input->do == 'submit')
        {
            $user = Bugdar::$auth->current_user();

            $title = trim($this->input->title);
            if (empty($title))
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_MISSING_TITLE')));

            $comment_body = trim($this->input->comment_body);
            if (empty($comment_body))
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('COMMENT_MISSING_BODY')));

            Bugdar::$db->BeginTransaction();
            {
                $now = time();

                $bug = new Bug();
                $bug->title             = $title;
                $bug->reporting_user_id = $user->user_id;
                $bug->reporting_date    = $now;
                $bug->Insert();
                $this->bug_id = $bug->bug_id;

                // Now create the first comment.
                $comment = new Comment();
                $comment->bug_id       = $bug->bug_id;
                $comment->post_user_id = $user->user_id;
                $comment->post_date    = $now;
                $comment->body         = $comment_body;
                $comment->Insert();
                $this->comment_id = $comment->comment_id;

                // Update the bug so it can find that first comment easiliy.
                $bug = new Bug($bug->bug_id);
                $bug->first_comment_id = $comment->comment_id;
                $bug->Update();
                $bug->FetchInto();
            }
            Bugdar::$db->Commit();

            $search = new SearchEngine();
            $search->IndexBug($bug);

            EventPump::Pump()->PostEvent(new StandardSuccessEvent('home', l10n::S('BUG_CREATED_SUCCESSFULLY')));
        }
    }
}
