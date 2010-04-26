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

require_once BUGDAR_ROOT . '/includes/model_user.php';

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
            $user = new User();
            $user->set_condition('email = :email');
            $user->email = $this->input->email;

            try {
                $user = $user->Fetch();
            }
            catch (phalanx\data\ModelException $e) {
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('LOGIN_FAILED')));
                return;
            }

            if ($user->password != md5(sha1($this->input->password) . $user->salt)) {
                EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('LOGIN_FAILED')));
                return;
            }

            // We need to set _COOKIE values so that if the last_event requires
            // authentication, we can return the correct state.
            $expires = time() + (60 * 60 * 5);
            $this->_SetCookie('bugdar_user', $user->user_id, $expires);
            $this->_SetCookie('bugdar_pass', $user->authkey, $expires);

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

            $this->successful = TRUE;
            EventPump::Pump()->PostEvent(($last_event ?: new StandardSuccessEvent('home', l10n::S('LOGIN_SUCCESSFUL'))));
            return;
        }

        // Find the first non-UserLoginEvent that was processed. If the event
        // hasn't been finished, then this event preempted it and we should
        // store its data so that the user can return to what she was doing.
        $events = EventPump::Pump()->GetAllEvents();
        foreach ($events as $event)
        {
            if (!($event instanceof $this) && $event->state() != EventPump::EVENT_FINISHED)
            {
                $this->last_event = base64_encode(serialize(array(get_class($event), $event->input)));
                break;
            }
        }
    }

    protected function _SetCookie($name, $value, $expires)
    {
        $_COOKIE[$name] = $value;
        setcookie($name, $value, $expires);
    }
}
