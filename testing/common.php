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

error_reporting(E_ALL & ~E_NOTICE);

// Define path constants.
define('BUGDAR_ROOT', dirname(dirname(__FILE__)));
define('PHALANX_ROOT', BUGDAR_ROOT . '/phalanx');
define('TEST_ROOT', dirname(__FILE__));

// Load some standard Bugdar files.
require_once BUGDAR_ROOT . '/includes/core.php';
require_once BUGDAR_ROOT . '/includes/localizer.php';
require_once BUGDAR_ROOT . '/includes/view_helpers.php';
require_once BUGDAR_ROOT . '/events/standard_error.php';
require_once BUGDAR_ROOT . '/events/standard_success.php';

// Read the configuration file.
$config_path = BUGDAR_ROOT . '/testing/config.php';
if (!file_exists($config_path) || !is_readable($config_path))
    throw new CoreException('Could not read TESTING configuration file');
$config = new phalanx\base\KeyDescender(require $config_path);

// Setup common functionality.
Bugdar::BootstrapDatabase($config);
