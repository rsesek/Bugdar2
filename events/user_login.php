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

// This event takes a username and password and logs the user in. THIS EVENT IS
// TEMPORARY and will replaced with the Rugdar 2 authentication API later.
class UserLoginEvent extends phalanx\events\Event
{
    // Whether the user logged in successfully.
    protected $successful = FALSE;
    public function was_successful() { return $this->successful; }

    static public function InputList()
    {
        return array(
            'do',
            'email',
            'password'
        );
    }

    static public function OutputList()
    {
        return array('successful');
    }

    public function WillFire()
    {
        // TODO: check if the user is already logged in.
    }

    public function Fire()
    {
        if ($this->input->do == 'fire')
        {
            $stmt = Bugdar::$db->Prepare("
                SELECT user_id, email, password, salt, authkey
                FROM " . TABLE_PREFIX . "users
                WHERE email = ?
            ");
            if (!$stmt->Execute(array($this->input->email)))
                throw new Exception('failed to execute!');

            $user = $stmt->FetchObject();
            if (!$user)
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('LOGIN_FAILED')));

            if ($user->password != md5($this->input->password . $user->salt))
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('LOGIN_FAILED')));

            $expires = time() + (60 * 60 * 5);
            setcookie('bugdar_user', $user->user_id, $expires);
            setcookie('bugdar_pass', $user->authkey, $expires);

            EventPump::Pump()->PostEvent(new StandardSuccessEvent('home', l10n::S('LOGIN_SUCCESSFUL')));
        }
    }
}
