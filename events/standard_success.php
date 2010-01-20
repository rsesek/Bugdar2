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

// The StandardSuccessEvent is used to indicate that a data-altering event
// completed successfully. In non-AJAX contexts, this usually means redirecting
// with a flash message. This is the complement to StandardErrorEvent.
class StandardSuccessEvent extends phalanx\events\Event
{
    // The place we are redirecting to.
    protected $location;

    // The success message.
    protected $message;

    static public function InputList()
    {
        return array();
    }

    static public function OutputList()
    {
        return array('message');
    }

    public function __construct($location, $message)
    {
        $this->location = $location;
        $this->message  = $message;
    }

    public function Fire()
    {
        header("Location: {$this->location}");
        phalanx\events\EventPump::Pump()->Terminate();
    }

    // Getters.
    public function location() { return $this->location; }
    public function message() { return $this->message; }
}
