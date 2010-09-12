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
      'BUG_LIST_TITLE' => 'List'
    );
    return $strings;
  }
}
