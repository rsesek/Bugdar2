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
require_once BUGDAR_ROOT . '/includes/model_user.php';

class Attribute extends phalanx\data\Model
{
  // Model properties.
  protected $table_prefix = TABLE_PREFIX;
  protected $table = 'attributes';
  protected $primary_key = 'title';
  protected $condition = 'title = :title';

  // Struct properties.
  protected $fields = array(
    'title',
    'description',
    'type',  // See constants below.
    'validator_pattern',  // Stores list options and string regex.
    'required',
    'default_value',  // String. Or TRUE for TYPE_DATE to mean today.
    'can_search',
    'color_foreground',
    'color_background'
  );

  // Types of attributes {{
    const TYPE_TEXT = 'text';
    const TYPE_BOOL = 'boolean';
    const TYPE_LIST = 'list';
    const TYPE_DATE = 'date';
    const TYPE_USER = 'user';
  // }}

  // Usergroup access controls {{
    const ACCESS_NONE  = 0;
    const ACCESS_READ  = 1;
    const ACCESS_WRITE = 2;
  // }}

  public function is_tag()
  {
    $title = $this->title;
    return empty($title);
  }
  public function is_attribute()
  {
    $title = $this->title;
    return !empty($title);
  }

  // Returns the access level that |user| has for this attribute for |bug|.
  public function CheckAccess(User $user, Bug $bug)
  {
    return self::ACCESS_READ | self::ACCESS_WRITE;
  }

  // Validates the value of an attribute. Returns a 2-Tuple<bool,mixed>. The
  // first item is whether or not the value validated. The second item is the
  // validated value, if any transformation took place.
  public function Validate($value)
  {
    switch ($this->type) {
      case self::TYPE_TEXT: return $this->_ValidateText($value);
      case self::TYPE_BOOL: return $this->_ValidateBoolean($value);
      case self::TYPE_LIST: return $this->_ValidateList($value);
      case self::TYPE_DATE: return $this->_ValidateDate($value);
      case self::TYPE_USER: return $this->_ValidateUser($value);
      default: throw new AttributeException('Unknown attribute type "' . $this->type . '"');
    }
  }

  protected function _ValidateText($value)
  {
    $value = trim($value);

    // Handle empty strings, including the default value.
    if ($this->required && empty($value) && !$this->default_value) {
      return array(FALSE, $value);
    } else if ($this->default_value) {
      return array(TRUE, $this->default_value);
    }

    // Validate using pattern.
    if ($this->validator_pattern) {
      $valid = preg_match("/{$this->validator_pattern}/", $value);
      return array($valid !== FALSE && $valid > 0, $value);
    }

    // All other values are valid.
    return array(TRUE, $value);
  }

  protected function _ValidateBoolean($value)
  {
    // Booleans are technically tri-state: true, false, and unset. The only
    // time the default value can be used is in the unset state.
    if ($value === NULL && $this->default_value !== NULL) {
      return array(TRUE, $this->default_value);
    }

    // Parse booleans in a bunch of different ways.
    $value = trim(strtolower($value));
    if (intval($value[0]) == 1 || $value[0] === TRUE ||
      $value[0] == 'y' || $value[0] == 't') {
      return array(TRUE, TRUE);
    }
    // Everything else will assume false. Don't bother failing validation.
    return array(TRUE, FALSE);
  }

  protected function _ValidateList($value)
  {
    // Handle empty values, including the default value.
    if ($this->required && empty($value) && !$this->default_value) {
      return array(FALSE, $value);
    } else if ($this->default_value) {
      return array(TRUE, $this->default_value);
    }

    // Otherwise, iterate over the possible values.
    $options = $this->GetListOptions();
    $value   = trim($value);
    foreach ($options as $option) {
      if (strcasecmp($option, $value) == 0) {
        // Return the proper case from the canonical option value.
        return array(TRUE, $option);
      }
    }
    return array(FALSE, $value);
  }

  protected function _ValidateDate($value)
  {
    // Handle the one default value (now).
    if ($this->required && empty($value) && !$this->default_value) {
      return array(FALSE, $value);
    } else if ($this->default_value) {
      return array(TRUE, time());
    }

    $time = strtotime($value);
    if ($time === FALSE) {
      return array(FALSE, $value);
    } else {
      return array(TRUE, $time);
    }
  }

  protected function _ValidateUser($value)
  {
    // Handle the default value.
    if ($this->required && empty($value) && !$this->default_value) {
      return array(FALSE, $value);
    } else if ($this->default_value) {
      return array(TRUE, $this->default_value);
    }

    // Look the user up by alias to get the user ID.
    $user = new User();
    $user->alias = $value;
    $user->set_condition('alias = :alias');
    try {
      $user->FetchInto();
      return array(TRUE, $user->user_id);
    } catch (\phalanx\data\ModelException $e) {
      return array(FALSE, $value);
    }
  }

  // If this Attribute is TYPE_LIST, this will return an array of options for
  // the list. Note that bugs store values rather than indices, so comparison
  // is case-insensitive string compare to determine if a value is a member
  // of the set.
  public function GetListOptions()
  {
    if ($this->type != self::TYPE_LIST) {
      throw new AttributeException('"' . $this->title . '" is not a list');
    }
    return explode("\n", $this->validator_pattern);
  }

  // Sets the valid options for the list. This will replace all current
  // options. Note that bugs will retain their current values if an option is
  // removed, as they store the actual value, rather than a reference to the
  // value.
  public function SetListOptions(Array $options)
  {
    if ($this->type != self::TYPE_LIST) {
      throw new AttributeException('"' . $this->title . '" is not a list');
    }
    $str_filter = '/[^a-z0-9_\-\.,]/i';
    foreach ($options as $i => $option) {
      $options[$i] = preg_replace($str_filter, '', $option);
    }
    $this->validator_pattern = implode("\n", $options);
  }
}

class AttributeException extends Exception
{}
