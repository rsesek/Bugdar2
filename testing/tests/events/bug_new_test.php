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

require_once BUGDAR_ROOT . '/events/bug_new.php';

class BugNewEventTest extends BugdarTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->_RequireAuthentication();
    }

    public function testBugNew()
    {
        $data = new phalanx\base\PropertyBag(array(
            'do'           => 'submit',
            'title'        => 'New Bug',
            'comment_body' => 'This is a Test Bug'
        ));
        $event = new BugNewEvent($data);
        $time = time();
        EventPump::Pump()->PostEvent($event);

        $bug = new Bug($event->bug_id());
        $bug->FetchInto();
        $this->assertEquals($data->title, $bug->title);
        $this->assertEquals(Bugdar::$auth->current_user()->user_id, $bug->reporting_user_id);
        $this->assertGreaterThanOrEqual($time, $bug->reporting_date);

        $comment = new Comment($event->comment_id());
        $comment->FetchInto();
        $this->assertEquals($bug->bug_id, $comment->bug_id);
        $this->assertEquals($bug->first_comment_id, $comment->comment_id);
        $this->assertEquals(Bugdar::$auth->current_user()->user_id, $comment->post_user_id);
        $this->assertGreaterThanOrEqual($time, $comment->post_date);
        $this->assertEquals($data->comment_body, $comment->body);
    }
}
