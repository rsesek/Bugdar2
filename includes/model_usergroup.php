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

class Usergroup extends phalanx\data\Model
{
  // Model properties.
  protected $table_prefix = TABLE_PREFIX;
  protected $table = 'usergroups';
  protected $primary_key = 'usergroup_id';
  protected $condition = 'usergroup_id = :usergroup_id';

  // Struct properties.
  protected $fields = array(
    'usergroup_id',
    'title',
    'display_title',
    'mask'
  );

  // Permission masks {{
    const CAN_VIEW        = 1;
    const CAN_REPORT      = 2;
    const CAN_VOTE        = 4;
    const CAN_COMMENT       = 8;
    const CAN_UPDATE      = 16;
    const CAN_VIEW_HIDDEN     = 32;
    const CAN_EDIT_OWN_COMMENTS = 64;
    const CAN_EDIT_ALL_COMMENTS = 128;
    const CAN_EDIT_REPORT     = 256;
    const CAN_DELETE_COMMENTS   = 512;
    const CAN_DELETE_REPORTS  = 1024;
  // }}

  // Default, built-in roles {{
    const ROLE_ANONYMOUS = 1;
    const ROLE_REGISTERED = 2;
    const ROLE_DEVELOPER = 3;
    const ROLE_ADMINISTRATOR = 4;
  // }}

  // Returns the Usergroup object that informs the permissions of all
  // unregistered users (guests).
  static public function AnonymousGroup()
  {
    $group = new self(self::ROLE_ANONYMOUS);
    $group->FetchInto();
    return $group;
  }
}
