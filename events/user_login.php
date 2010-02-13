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

// This is the standard Bugdar 2 login system. It uses user_login.tpl to
// display its login form and will then authenticate the user and set cookies.
// If you are looking to change the credential system in Bugdar, take a look
// at includes/auth/auth.php.
class UserLoginEvent extends phalanx\events\Event
{
    // Whether the user logged in successfully.
    protected $successful = FALSE;
    public function was_successful() { return $this->successful; }

    // The last event. This is serialized in base64.
    protected $last_event = NULL;
    public function last_event() { return $this->last_event; }

    static public function InputList()
    {
        return array(
            'do',
            'email',
            'password',
            'last_event'
        );
    }

    static public function OutputList()
    {
        return array(
            'successful',
            'last_event'
        );
    }

    public function WillFire()
    {
        if (Bugdar::$auth->IsLoggedIn())
            $this->Cancel();
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

            // We need to set _COOKIE values so that if the last_event requires
            // authentication, we can return the correct state.
            $expires = time() + (60 * 60 * 5);
            $_COOKIE['bugdar_user'] = $user->user_id;
            $_COOKIE['bugdar_pass'] = $user->authkey;
            setcookie('bugdar_user', $_COOKIE['bugdar_user'], $expires);
            setcookie('bugdar_pass', $_COOKIE['bugdar_pass'], $expires);

            $last_event = NULL;
            if ($this->input->last_event)
            {
                $last_event = unserialize(base64_decode($this->input->last_event));
                $class      = $last_event[0];
                $input      = $last_event[1];
                if (!class_exists($class))
                {
                    $path = phalanx\base\CamelCaseToUnderscore($class);
                    $path = preg_replace('/_event$/', '', $path);
                    require_once BUGDAR_ROOT . "/events/$path.php";
                }
                $last_event = new $class($input);
            }

            EventPump::Pump()->PostEvent(($last_event ?: new StandardSuccessEvent('home', l10n::S('LOGIN_SUCCESSFUL'))));
            return;
        }

        // Check and see if this login event preempted some other event.
        $events = EventPump::Pump()->GetAllEvents();
        if ($events->Count() >= 3)
        {
            foreach ($events as $tuple)
            {
                list($state, $object) = $tuple;
                // If we find an event that isn't a UserLoginEvent that hasn't
                // been finished, then that's the last event.
                if (!($object instanceof $this) && $state != EventPump::EVENT_FINISHED)
                {
                    $this->last_event = base64_encode(serialize(array(get_class($object), $object->input)));
                    break;
                }
            }
        }
    }
}
