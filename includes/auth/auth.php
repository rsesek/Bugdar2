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

// This is the base authentication system class. The Bugdar 2 authentication
// API is significantly more flexible than the version 1 API. However, it also
// means implementors are required to do more work sometimes. The system is
// action-based rather than fill-in-the-puzzle based; that is, rather than
// asking for specific pieces for data, we now ask the question that was
// driving the need for that data.
//
// All functions can be safely overridden if need be. Conforming to the
// behavior  outlined above each function is the only requirement.
//
// NOTE: This API is NOT YET FINALIZED and is liable to change. Bugdar 2 is
// currently in the alpha stage of development. YOU HAVE BEEN WARNED.
abstract class Authentication
{
    // The auth section of the config file.
    protected $auth_config = NULL;

    // The currently logged-in user.
    protected $current_user = NULL;

    public function __construct(Array $config)
    {
        $this->auth_config = $config;
    }

    // Should return FALSE if there is not a logged in user, or the user's
    // information dictionary in |$this->current_user|. Implementers are
    // responsible for setting that variable properly.
    abstract public function IsLoggedIn();

    // Callers can use this to put up a login page if the user is not logged
    // in. This is a forceful version of IsLoggedIn().
    public function RequireAuthentication()
    {
        if ($this->IsLoggedIn())
            return $this->current_user();
        $this->_PerformLogin();
    }

    // Public wrapper around _PerformLogout() that NULLs |$this->current_user|.
    public function Logout()
    {
        $this->current_user = NULL;
        $this->_PerformLogout();
    }

    // Implementers should perform whatever login task they want here. You
    // could redirect off-site, access a different database, read a text file,
    // or anything else. Be sure to END EXECUTION in this function to prevent
    // unauthorized access. You are not required to set |$this->current_user|.
    abstract protected function _PerformLogin();

    // This should clear whatever login cookies or session information has been
    // set. Do not interrupt execution from within this method.
    abstract protected function _PerformLogout();

    // Getters.
    // ------------------------------------------------------------------------
    public function current_user() { return $this->current_user; }
}
