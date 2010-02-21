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

// Fix up the include path for the Zend framework. This file should always be
// REQUIRE_ONCE included.
$path = ini_get('include_path');
ini_set('include_path', $path . ':' . BUGDAR_ROOT . '/zend_lucene');

require_once BUGDAR_ROOT . '/zend_lucene/Zend/Search/Lucene.php';

class SearchEngine
{
    // The Zend Lucene object.
    protected $lucene = NULL;

    public function __construct()
    {
        $index_path = BUGDAR_ROOT. '/cache/lucene_index';
        if (file_exists($index_path))
            $this->lucene = Zend_Search_Lucene::Open($index_path);
        else
            $this->lucene = Zend_Search_Lucene::Create($index_path);
    }

    public function IndexBug($obj)
    {
        $bug = new phalanx\base\KeyDescender($obj);
        $this->RemoveBug($bug->bug_id);

        $doc = new Zend_Search_Lucene_Document();
        $doc->AddField(Zend_Search_Lucene_Field::Keyword('bug_id', $bug->bug_id));
        $doc->AddField(Zend_Search_Lucene_Field::Text('title', $bug->title));
        $doc->AddField(Zend_Search_Lucene_Field::Keyword('reporting_user_id', $bug->reporting_user_id));
        $doc->AddField(Zend_Search_Lucene_Field::Keyword('reporting_date', $bug->reporting_date));

        // We concatenate all comments into a single text blob. We only show
        // hits as bugs, but we want comment content to matter.
        $comment_blob = '';
        $stmt = Bugdar::$db->Prepare("SELECT body FROM " . TABLE_PREFIX . "comments WHERE bug_id = ? ORDER BY comment_id");
        $stmt->Execute(array($bug->bug_id));
        while ($comment = $stmt->FetchObject())
            $comment_blob .= $comment->body . "\n\n";
        $doc->AddField(Zend_Search_Lucene_Field::UnStored('comments', $comment_blob));

        // Add all attributes.
        $stmt = Bugdar::$db->Prepare("SELECT * FROM " . TABLE_PREFIX . "bug_attributes WHERE bug_id = ?");
        $stmt->Execute(array($bug->bug_id));
        $tags = array();
        while ($attr = $stmt->FetchObject())
            if ($attr->attribute_title)
                $doc->AddField(Zend_Search_Lucene_Field::Keyword($attr->attribute_title, $attr->value));
            else
                $tags[] = $attr->value;
        $doc->AddField(Zend_Search_Lucene_Field::Text('tag', implode(' ', $tags)));

        $this->lucene->AddDocument($doc);
    }

    protected function _GetLuceneBugDocument($bug_id)
    {
        $term  = new Zend_Search_Lucene_Index_Term($bug_id, 'bug_id');
        $hits  = $this->lucene->TermDocs($term);
        if (count($hits) >= 1)
            return $hits[0];
        return NULL;
    }

    public function RemoveBug($bug_id)
    {
        $doc = $this->_GetLuceneBugDocument($bug_id);
        if ($doc)
            $this->lucene->Delete($doc->id);
    }

    public function SearchByQueryString($query_string)
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse($query_string);
        return $this->lucene->Find($query);
    }
}

$search = new SearchEngine();
