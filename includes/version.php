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

namespace bugdar;

class Version
{
  const Major      = 2;
  const Minor      = 0;
  const PatchLevel = 0;

  const Stage        = 'alpha';
  const StageVersion = 1;

  static public function PresentationString()
  {
    $string = sprintf('%d.%d', self::Major, self::Minor);
    if (self::PatchLevel) {
      $string = sprintf('%s.%d', $string, self::PatchLevel);
    }
    if (self::Stage) {
      return sprintf('%s %s %d', $string, self::Stage, self::StageVersion);
    }
    return $string;
  }
}
