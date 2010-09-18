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

require_once BUGDAR_ROOT . '/includes/language.php';

class Language_en_US extends \bugdar\Language
{
  public function code()
  {
    return 'en-US';
  }

  public function title()
  {
    return 'English (US)';
  }

  public function text_direction()
  {
    return 'ltr';
  }

  public function & strings()
  {
    static $strings = array(
      'BUG_LIST_TITLE' => 'List',

      'BUG_NEW_TITLE' => 'New Report',

      'USER_LOGIN_TITLE' => 'Login',
      'USER_LOGIN_EMAIL' => 'Email',
      'USER_LOGIN_PASSWORD' => 'Password',

      'ADMIN_SETTINGS_TITLE' => 'System Options',
      'ADMIN_SETTINGS_TRACKER_NAME_VAR' => 'Tracker Name',
      'ADMIN_SETTINGS_TRACKER_NAME_DFN' => 'The name of this bug tracker, which will appear in page titles and email subjects.',
      'ADMIN_SETTINGS_WEBROOT_VAR' => 'Web Root',
      'ADMIN_SETTINGS_WEBROOT_DFN' => 'The absolute path from the server root to the Bugdar installation.  This should end with a trailing slash.',
      'ADMIN_SETTINGS_SAVE' => 'Save Settings',
    );
    return $strings;
  }
}
