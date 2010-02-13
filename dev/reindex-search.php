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

// This script will re-index the search system. It is meant to be run at
// the TOP LEVEL of Bugdar's directory.

define('BUGDAR_ROOT', getcwd());
define('PHALANX_ROOT', BUGDAR_ROOT . '/phalanx');

require_once BUGDAR_ROOT . '/includes/core.php';
require_once BUGDAR_ROOT . '/includes/search_engine.php';

// Load the database.
$config = new phalanx\base\KeyDescender(require BUGDAR_ROOT . '/includes/config.php');
Bugdar::BootstrapDatabase($config);

// This is going to take a while...
set_time_limit(0);
ini_set('max_execution_time', 0);

// Destroy the old index and create a new one.
echo 'Destroying old index for ' . BUGDAR_ROOT . "\n";
exec('rm -rf ' . BUGDAR_ROOT . '/cache/lucene_index');
clearstatcache();

echo 'New index created' . "\n";
$search = new SearchEngine();

$bugs = Bugdar::$db->Query("SELECT * FROM " . TABLE_PREFIX . "bugs");
while ($bug = $bugs->FetchObject())
{
    $search->IndexBug($bug);
    echo ".";
}

echo "\nDone!\n";
