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

// This event creates a new comment on a bug.
class CommentNewEvent extends phalanx\events\Event
{
  protected $comment_id = 0;
  public function comment_id() { return $this->comment_id; }

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
    if ($this->input->do == 'submit') {
      $bug = new Bug($this->input->bug_id);
      try {
        $bug->FetchInto();
      } catch (phalanx\data\ModelException $e) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_ID_NOT_FOUND')));
        return;
      }

      $body = trim($this->input->body);
      if (empty($body)) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('COMMENT_MISSING_BODY')));
        return;
      }

      $comment = new Comment();
      $comment->bug_id       = $bug_id;
      $comment->post_user_id = Bugdar::$auth->current_user();
      $comment->post_date    = time();
      $comment->body         = $body;
      $comment->Insert();
      $this->comment_id = $comment->comment_id;

      $search = new SearchEngine();
      $search->IndexBug($bug);

      EventPump::Pump()->PostEvent(new StandardSuccessEvent('view_bug/' . $bug_id, l10n::S('USER_REGISTER_SUCCESS')));
    }
  }
}
