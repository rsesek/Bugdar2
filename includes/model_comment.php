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

require_once PHALANX_ROOT . '/data/model.php';

class Comment extends phalanx\data\Model
{
  // Model properties.
  protected $table_prefix = TABLE_PREFIX;
  protected $table = 'comments';
  protected $primary_key = 'comment_id';
  protected $condition = 'comment_id = :comment_id';

  // Struct properties.
  protected $fields = array(
  	'comment_id',
  	'bug_id',
  	'post_user_id',
  	'post_date',
  	'hidden',
  	'body'
  );
}
