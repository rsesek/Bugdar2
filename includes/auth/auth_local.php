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

require_once BUGDAR_ROOT . '/includes/auth/auth.php';
require_once BUGDAR_ROOT . '/includes/model_user.php';
require_once BUGDAR_ROOT . '/events/user_login.php';

// AuthenticationLocal uses the default Bugdar 2 user database. It should be
// suitable for almost everyone.
class AuthenticationLocal extends Authentication
{
  public function IsLoggedIn()
  {
    if ($this->current_user())
      return $this->current_user();

    $user = new User($_COOKIE['bugdar_user']);
    try {
      $user->FetchInto();
    }
    catch (phalanx\data\ModelException $e) {
      return FALSE;
    }

    if ($user->authkey != $_COOKIE['bugdar_pass'])
      return FALSE;

    $this->current_user = $user;
    return $this->current_user;
  }

  protected function _PerformLogin()
  {
    phalanx\events\EventPump::Pump()->RaiseEvent(new UserLoginEvent());
    phalanx\events\EventPump::Pump()->StopPump();
  }

  protected function _PerformLogout()
  {
    setcookie('bugdar_user');
    setcookie('bugdar_pass');
  }
}
