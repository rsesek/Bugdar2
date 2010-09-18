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
  static public function InputList()
  {
    return array(
      'bug_id',
      'comment_body',
      'attributes',
      'tags_new',
      'tags_deleted'
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
    $bug  = new Bug($this->input->bug_id);
    $user = Bugdar::$auth->current_user();

    try {
      $bug->FetchInto();
    } catch (\phalanx\data\ModelException $e) {
      EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('BUG_ID_NOT_FOUND')));
      return;
    }

    Bugdar::$db->BeginTransaction();

    // Add a comment if one is present.
    $body = trim($this->input->comment_body);
    if (!empty($body)) {
      $comment = new Comment();
      $comment->bug_id     = $bug->bug_id;
      $comment->post_user_id = $user->user_id;
      $comment->post_date  = time();
      $comment->body     = $body;
      $comment->Insert();
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

    Bugdar::$db->Commit();

    $search = new SearchEngine();
    $search->IndexBug($bug);

    EventPump::Pump()->PostEvent(new StandardSuccessEvent('view_bug/' . $bug->bug_id, l10n::S('USER_REGISTER_SUCCESS')));
  }
}
