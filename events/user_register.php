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

// This event takes some basic user data and will create a user account,
// returning the new user's ID.
class UserRegisterEvent extends phalanx\events\Event
{
  protected $user_id = 0;
  public function user_id() { return $this->user_id; }

  static public function InputList()
  {
    return array(
      'do',
      'email',
      'alias',
      'password'
    );
  }

  static public function OutputList()
  {
    return array('user_id');
  }

  public function WillFire()
  {
    if (Bugdar::$auth->IsLoggedIn())
      $this->Cancel();
  }

  public function Fire()
  {
    if ($this->input->do == 'submit') {
      if (!filter_var($this->input->email, FILTER_VALIDATE_EMAIL)) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('INVALID_EMAIL')));
        return;
      }

      if (strlen($this->input->password) <= 4) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('PASSWORD_TOO_SHORT')));
        return;
      }

      $stmt = Bugdar::$db->Prepare("SELECT COUNT(*) AS count FROM users WHERE email = ?");
      $stmt->Execute(array($this->input->email));
      if ($stmt->FetchObject()->count > 0) {
        EventPump::Pump()->RaiseEvent(new StandardErrorEvent(l10n::S('EMAIL_IN_USE')));
        return;
      }

      $alias = preg_replace('/[^a-zA-Z0-9\-_,\. ]/', '', $this->input->alias);
      $salt  = phalanx\base\Random(10);

      $user = new User();
      $user->email        = $this->input->email;
      $user->alias        = preg_replace('/[^a-zA-Z0-9\-_,\. ]/', '', $this->input->alias);
      $user->password     = sha1($this->input->password);
      $user->usergroup_id = Usergroup::ROLE_REGISTERED;
      $user->Insert();
      $this->user_id = $user->user_id;

      EventPump::Pump()->PostEvent(new StandardSuccessEvent('login', l10n::S('USER_REGISTER_SUCCESS')));
    }
  }
}
