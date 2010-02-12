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

// This event allows viewing of a user profile.
class UserViewEvent extends phalanx\events\Event
{
    protected $user = NULL;
    public function user() { return $this->user; }

    static public function InputList()
    {
        return array(
            '_id'
        );
    }

    static public function OutputList()
    {
        return array('user');
    }

    public function Fire()
    {
        $stmt = Bugdar::$db->Prepare("SELECT * FROM " . TABLE_PREFIX . "users WHERE user_id = :id OR alias = :id");
        $stmt->Execute(array('id' => $this->input->_id));
        if (!($this->user = $stmt->FetchObject()))
            EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('INVALID_USER')));
    }
}
