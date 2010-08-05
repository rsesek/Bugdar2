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

require_once BUGDAR_ROOT . '/events/bug_edit.php';

class BugEditEventTest extends BugdarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_RequireAuthentication();
    }

    protected function _MakeBug()
    {
        $bug = new Bug();
        $bug->title             = 'Bug Edit Test';
        $bug->reporting_user_id = Bugdar::$auth->current_user()->user_id;
        $bug->reporting_date    = time();
        $bug->Insert();
        $comment = new Comment();
        $comment->body = 'Testing 1 2 3';
        $comment->bug_id = $bug->bug_id;
        $comment->Insert();
        $bug->first_comment_id  = $comment->comment_id;
        $bug->Update();
        return $bug;
    }

    public function testAddTags()
    {
        $bug = $this->_MakeBug();

        $data = new \phalanx\base\PropertyBag(array(
            'bug_id'    => $bug->bug_id,
            'tags_new'  => array('red', 'green', 'blue')
        ));
        $event = new BugEditEvent($data);
        EventPump::Pump()->PostEvent($event);

        $attrs = $bug->FetchAttributes();
        $this->assertEquals(3, count($attrs));
        foreach ($attrs as $attr) {
            $this->assertTrue(empty($attr->title));
            $this->assertTrue(in_array($attr->value, $data->tags_new));
        }

        return $bug;
    }

    public function testRemoveTags()
    {
        $bug = $this->testAddTags();

        $data = new \phalanx\base\PropertyBag(array(
            'bug_id'       => $bug->bug_id,
            'tags_deleted' => array('red', 'blue')
        ));
        $event = new BugEditEvent($data);
        EventPump::Pump()->PostEvent($event);

        // Re-query the bug because the attribute cache is stale due to the
        // attributes being modified by another instance of the Model object.
        $bug->FetchInto();
        $attrs = $bug->FetchAttributes();
        $this->assertEquals(1, count($attrs));
        $this->assertEquals('green', $attrs[0]->value);
    }

    public function testAddComment()
    {
        $bug = $this->_MakeBug();

        $data = new \phalanx\base\PropertyBag(array(
            'bug_id'        => $bug->bug_id,
            'comment_body'  => 'Second comment.'
        ));
        $event = new BugEditEvent($data);
        EventPump::Pump()->PostEvent($event);

        $comments = $bug->FetchComments();
        $this->assertEquals(2, count($comments));
        $this->assertEquals('Testing 1 2 3', $comments[0]->body);
        $this->assertEquals($data->comment_body, $comments[1]->body);
    }
}