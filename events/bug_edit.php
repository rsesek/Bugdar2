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

require_once BUGDAR_ROOT . '/includes/model_attribute.php';
require_once BUGDAR_ROOT . '/includes/model_bug.php';
require_once BUGDAR_ROOT . '/includes/model_comment.php';
require_once BUGDAR_ROOT . '/includes/search_engine.php';

// This event creates a new comment on a bug.
class BugEditEvent extends phalanx\events\Event
{
  protected $bug_id = 0;
  public function bug_id() { return $this->bug_id; }

  protected $comment_id = 0;
  public function comment_id() { return $this->comment_id; }

  protected $action = '__unset_operation__';
  public function action() { return $this->action; }

  static public function InputList()
  {
    return array(
      'action',
      'bug_id',
      'title',
      'comment_body',
      'attributes',
      'tags_new',
      'tags_deleted'
    );
  }

  static public function OutputList()
  {
    return array(
      'action',
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
    $do_insert = ($this->input->action == 'insert');
    $do_update = ($this->input->action == 'update');
    if ($this->input->_method != 'POST') {
      EventPump::Pump()->RaiseEvent(new StandardErrorEvent('Request must be POSTed'));
      return;
    }

    // Create an empty Model object if creating a new bug, or fetch the data of
    // an existing bug to update.
    if ($do_insert) {
      $bug = new Bug();
    } else if ($do_update) {
      $bug  = new Bug($this->input->bug_id);
      try {
        $bug->FetchInto();
      } catch (\phalanx\data\ModelException $e) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_ID_NOT_FOUND')));
        return;
      }
    } else {
      EventPump::Pump()->RaiseEvent(new StandardErrorEvent('Invalid bug operation'));
      return;
    }
    $this->action = $this->input->action;

    $user = Bugdar::$auth->current_user();

    $title = trim($this->input->title);
    if (empty($title) && $do_insert) {
      EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_MISSING_TITLE')));
      return;
    }

    Bugdar::$db->BeginTransaction();
    {
      $now = time();
      if (!empty($title)) {
        $bug->title = $title;
      }

      if ($do_insert) {
        $bug->reporting_user_id = $user->user_id;
        $bug->reporting_date    = $now;
        $bug->Insert();
      } else if ($do_update) {
        $bug->Update();
      }

      // Now set the bug_id output value, which will be set after a call to
      // Insert().  Updated bugs will have this set from FetchInto().
      $this->bug_id = $bug->bug_id;

      // Add a comment if one is present.
      $body = trim($this->input->comment_body);
      if (!empty($body) || $do_insert) {
        if ($do_insert && empty($body)) {
          EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('COMMENT_MISSING_BODY')));
          return;
        }
        $comment = new Comment();
        $comment->bug_id       = $this->bug_id;
        $comment->post_user_id = $user->user_id;
        $comment->post_date    = $now;
        $comment->body         = $body;
        $comment->Insert();
        $this->comment_id = $comment->comment_id;

        // Update the bug so it can find that first comment easiliy.
        if ($do_insert) {
          $bug = new Bug($bug->bug_id);
          $bug->first_comment_id = $comment->comment_id;
          $bug->Update();
          $bug->FetchInto();
        }
      }

      // Handle tags.
      if (is_array($this->input->tags_new)) {
        foreach ($this->input->tags_new as $tag) {
          $bug->SetAttribute('', $tag);
        }
      }
      if (is_array($this->input->tags_deleted)) {
        foreach ($this->input->tags_deleted as $tag) {
          $bug->RemoveAttribute($tag, TRUE);
        }
      }

      // Create a map of all the set attributes.
      $set_attributes = array();
      if (is_array($this->input->attributes)) {
        foreach ($this->input->attributes as $attr) {
          // If this is an empty attribute, ignore it.
          if (empty($attr['title']) || empty($attr['value'])) {
            continue;
          }
          $set_attributes[$attr['title']] = $attr['value'];
        }

        // Get all potential attributes; this includes defined tags.
        $attributes = Attribute::FetchGroup();
        foreach ($attributes as $attr) {
          // If the user is allowed to write to this attribute, update the
          // value.
          if ($attr->is_attribute() && $attr->CheckAccess($user, $bug) & Attribute::ACCESS_WRITE) {
            // If there is no value for this attribute, then it was removed.
            if (!isset($set_attributes[$attr->title])) {
              $bug->RemoveAttribute($attr->title, $attr->is_tag());
            }

            // Otherwise, update the value.
            $validate = $attr->Validate($set_attributes[$attr->title]);
            if ($validate[0]) {
              $bug->SetAttribute($attr->title, $validate[1]);
            }
          }
        }
      }
    }
    Bugdar::$db->Commit();

    $search = new SearchEngine();
    $search->IndexBug($bug);

    $string = ($do_insert) ? l10n::S('BUG_CREATED_SUCCESSFULLY') : l10n::S('BUG_EDIT_SUCCESS');
    EventPump::Pump()->PostEvent(new StandardSuccessEvent('view_bug/' . $this->bug_id, $string));
  }
}
