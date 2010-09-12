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

// This is an abstract class that language packs inherit from.
abstract class Language
{
  // Returns the IETF Language tag for this language (RFC 5646).
  abstract public function code();

  // Returns the human-readable title for the language, used for dispaly.
  abstract public function title();

  // Gets the text direction; should be either "ltr" or "rtl".
  abstract public function text_direction();

  // Returns the array of string name => value pairs.  Return by reference.
  abstract public function & strings();
}
