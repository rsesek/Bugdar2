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

use phalanx\events\EventPump as EventPump;

require_once BUGDAR_ROOT . '/includes/search_engine.php';

class SearchEvent extends phalanx\events\Event
{
    protected $hits = array();
    public function hits() { return $this->hits; }

    static public function InputList()
    {
        return array(
            'do',
            'query_string',
        );
    }

    static public function OutputList()
    {
        return array(
            'hits'
        );
    }

    public function Fire()
    {
        if ($this->input->do == 'search')
        {
            $search  = new SearchEngine();
            $results = $search->SearchByQueryString($this->input->query_string);

            $hits    = array();
            $id_list = array();
            foreach ($results as $result)
            {
                $hits[$result->bug_id] = $result;
                $id_list[] = $result->bug_id;
            }

            if (count($id_list) < 1)
                return;

            $bugs = Bugdar::$db->Query("
                SELECT bugs.*, users.alias as reporting_alias
                FROM " . TABLE_PREFIX . "bugs bugs
                LEFT JOIN " . TABLE_PREFIX . "users users
                    ON (bugs.reporting_user_id = users.user_id)
                WHERE bugs.bug_id IN (" . implode(',', $id_list) . ")
                LIMIT 30
            ");
            while ($bug = $bugs->FetchObject())
            {
                $lucene_hit = $hits[$bug->bug_id];
                $hits[$bug->bug_id] = $bug;
                $hits[$bug->bug_id]->lucene_hit = $lucene_hit;
                $hits[$bug->bug_id]->score      = $lucene_hit->score;
            }
            $this->hits = $hits;
        }
    }
}
