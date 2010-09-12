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

require_once BUGDAR_ROOT . '/languages/en-US.php';

class l10n
{
    // Singleton instance.
    static private $instance = NULL;

    // Histogram of missing strings.
    protected $missing_strings = array();

    // The Language object.
    protected $language = NULL;

    static public function S($string)
    {
        return self::Instance()->GetString($string);
    }

    protected function __construct()
    {
        $this->language = new Language_en_US();
    }

    public function GetString($string)
    {
        $strings = $this->GetLanguage()->strings();
        if (!isset($strings[$string])) {
            if (!isset($this->missing_strings[$string]))
                $this->missing_strings[$string] = 0;
            $this->missing_strings[$string]++;
            return $string;
        }
        return $strings[$string];
    }

    public function GetLanguage()
    {
        return $this->language;
    }

    // Getters and setters.
    // ------------------------------------------------------------------------
    static public function Instance()
    {
        if (!self::$instance)
            self::$instance = new l10n();
        return self::$instance;
    }
    static public function set_instance(l10n $instance) { self::$instance = $instance; }
}
