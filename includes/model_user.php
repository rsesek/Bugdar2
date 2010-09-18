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

require_once PHALANX_ROOT . '/data/model.php';
require_once BUGDAR_ROOT . '/includes/model_usergroup.php';

class User extends phalanx\data\Model
{
  // Model properties.
  protected $table_prefix = TABLE_PREFIX;
  protected $table = 'users';
  protected $primary_key = 'user_id';
  protected $condition = 'user_id = :user_id';

  // Struct properties.
  protected $fields = array(
    'user_id',
    'email',
    'alias',
    'usergroup_id',
    'other_usergroup_ids',
    'password',
    'salt',
    'authkey',
    'show_email',
    'language_id',
    'timezone',
    'user_auth_id'
  );

  // Insert() expects the |password| property to be in sha1 format.  It will
  // generate a salt and perform the second hash.
  public function Insert()
  {
    $this->salt     = phalanx\base\Random(10);
    $this->password = md5($this->password /*sha1*/ . $this->salt);
    $this->authkey  = \phalanx\base\Random();
    parent::Insert();
  }

  // Returns an array of Usergroups to which the user belongs. The array will
  // always contain at least 1 element at index 0: the primary usergroup.
  public function FetchUsergroups()
  {
    $group_list = $this->usergroup_id;
    if (strlen($this->other_usergroup_ids))
      $group_list .= ',' . $this->other_usergroup_ids;
    return Usergroup::FetchGroup('usergroup_id IN (' . $group_list . ')');
  }

  // Checks whether this user can access |$mask|.
  public static function CheckGroupPermission($mask)
  {
    $groups = $this->FetchUsergroups();
    $can_view = FALSE;
    foreach ($groups as $group) {
      $can_view |= $group->mask & $mask;
    }
    return $can_view;
  }
}
