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

define('PHALANX_ROOT', dirname(__FILE__) . '/phalanx');

require_once PHALANX_ROOT . '/base/functions.php';
require_once PHALANX_ROOT . '/events/event.php';
require_once PHALANX_ROOT . '/events/event_pump.php';
require_once PHALANX_ROOT . '/events/http_dispatcher.php';
require_once PHALANX_ROOT . '/events/view_output_handler.php';
require_once PHALANX_ROOT . '/input/cleaner.php';
require_once PHALANX_ROOT . '/views/view.php';

$dispatcher   = new phalanx\events\HTTPDispatcher();
$view_handler = new phalanx\events\ViewOutputHandler();
$pump         = phalanx\events\EventPump::Pump();

// Use the Views system to render output.
// TODO: enable webservice and CLI.
$pump->set_output_handler($view_handler);

// Events are named in reverse order to be more human readable. Rather than
// user_register, it's register_user. After reversing the components, events
// are loaded from the events/ directory. The class name is the CammelCase
// version of the underscored name, suffixed by the word 'Event'.
$dispatcher->set_event_loader(
    function($name)
    {
        $name  = preg_replace('/[^a-z0-9_\-\.]/i', '', $name);
        $parts = explode('_', $name);
        $parts = array_reverse($parts);
        $name  = implode('_', $parts);
        $path = "./events/$name.php";
        if (!file_exists($path))
            // TODO: raise a 404 event.
            phalanx\events\EventPump::Pump()->Terminate('Could not load event ' . $name);
        require_once $path;
        return phalanx\base\UnderscoreToCamelCase($name) . 'Event';
    }
);

// Transform the event name into a template name.
phalanx\views\View::set_template_path(dirname(__FILE__) . '/templates/%s.tpl');
phalanx\views\View::set_cache_path(dirname(__FILE__) . '/cache');
$view_handler->set_template_loader(
    function($event_class)
    {
        $name = preg_replace('/Event$/', '', $event_class);
        return phalanx\base\CamelCaseToUnderscore($name);
    }
);

$dispatcher->Start();

$pump->StopPump();
