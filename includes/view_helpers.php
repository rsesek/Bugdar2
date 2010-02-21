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

use phalanx\base\KeyDescender as KeyDescender;
use phalanx\events\EventPump as EventPump;
use phalanx\data\Cleaner as Cleaner;

// Returns the web root URL().
function WebRoot($params = NULL)
{
    // BUG:K002 : view_helpers.php WebRoot() uses a hard-coded path
    $url = '/greenfield/';
    if (!$params)
        return $url;
    return $url . $params;
}

// This creates a link to another event. This should be an event class name (in
// camel case and all). The params will be passed via a GET URL.
function EventLink($event, $params = NULL)
{
    // Determine the base URL.
    // BUG:K001 : view_helpers.php EventLink() uses a hard-coded path
    $url = '/greenfield/';

    // Use the ViewOutputHandler's closure to convert the class name to viewese.
    // We then reverse new_comment to get comment_new.
    $f = EventPump::Pump()->output_handler()->template_loader();
    $parts = explode('_', $f($event));
    $parts = array_reverse($parts);
    $url .= implode('_', $parts);

    // Append parameters.
    if ($params !== NULL)
    {
        if (KeyDescender::IsDescendable($params))
        {
            foreach ($params as $key => $value)
            {
                $url .= '/' . Cleaner::HTML($key) . '/' . Cleaner::HTML($value);
            }
        }
        else
        {
            // This is a single-value type. HTML encode it and append it as the _id
            // parameter.
            $url .= '/' . Cleaner::HTML($params);
        }
    }

    return $url;
}
