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
      'ERROR_INVALID_ID' => 'Could not find object with that ID.',
      'BUTTON_SAVE_CHANGES' => 'Save Changes',
      'BUTTON_RESET' => 'Reset',
      'BUTTON_DELETE' => 'Delete',

      'BUG_LIST_TITLE' => 'List',

      'BUG_NEW_TITLE' => 'New Report',
      'BUG_EDIT_TITLE' => 'Bug #%d: %s',

      'USER_LOGIN_TITLE' => 'Login',
      'USER_LOGIN_EMAIL' => 'Email',
      'USER_LOGIN_PASSWORD' => 'Password',

      'ADMIN_SETTINGS_TITLE' => 'System Options',
      'ADMIN_SETTINGS_TRACKER_NAME_VAR' => 'Tracker Name',
      'ADMIN_SETTINGS_TRACKER_NAME_DFN' => 'The name of this bug tracker, which will appear in page titles and email subjects.',
      'ADMIN_SETTINGS_WEBROOT_VAR' => 'Web Root',
      'ADMIN_SETTINGS_WEBROOT_DFN' => 'The absolute path from the server root to the Bugdar installation.  This should end with a trailing slash.',
      'ADMIN_SETTINGS_SAVE' => 'Save Settings',

      'ADMIN_USERGROUPS_TITLE' => 'Usergroups',
      'ADMIN_USERGROUP_NEW' => 'Create New Usergroup',
      'ADMIN_USERGROUP_TITLE' => 'Usergroup Title',
      'ADMIN_USERGROUP_DISPLAY_TITLE' => 'Display Title',
      'ADMIN_USERGROUP_HAS_MASK' => 'Group Type',
      'ADMIN_USERGROUP_ROLE_GROUP' => 'Role Group',
      'ADMIN_USERGROUP_PURE_GROUP' => 'User Group',
      'ADMIN_USERGROUP_PERMISSIONS' => 'Permissions',
      'ADMIN_USERGROUP_PERMISSION_CAN_VIEW' => 'Can View Bug Reports',
      'ADMIN_USERGROUP_PERMISSION_CAN_REPORT' => 'Can Create New Bug Reports',
      'ADMIN_USERGROUP_PERMISSION_CAN_VOTE' => 'Can Vote on Bugs',
      'ADMIN_USERGROUP_PERMISSION_CAN_COMMENT' => 'Can Leave Comments on Bugs',
      'ADMIN_USERGROUP_PERMISSION_CAN_UPDATE' => 'Can Modify Bug Fields',
      'ADMIN_USERGROUP_PERMISSION_CAN_VIEW_HIDDEN' => 'Can View Hidden Bugs',
      'ADMIN_USERGROUP_PERMISSION_CAN_EDIT_OWN_COMMENTS' => 'Can Edit Own Comments',
      'ADMIN_USERGROUP_PERMISSION_CAN_EDIT_ALL_COMMENTS' => 'Can Edit All Comments',
      'ADMIN_USERGROUP_PERMISSION_CAN_EDIT_REPORT' => 'Can Edit Bug Reports',
      'ADMIN_USERGROUP_PERMISSION_CAN_DELETE_COMMENTS' => 'Can Delete Comments',
      'ADMIN_USERGROUP_PERMISSION_CAN_DELETE_REPORTS' => 'Can Delete Bugs',
    );
    return $strings;
  }
}
