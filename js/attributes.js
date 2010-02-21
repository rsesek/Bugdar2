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



// This adds another field row at the end of elemnt[id="fields"] based on the
// template it finds at element[id="field-template"].
last_field_id_ = 0;
function AddAttribute(title_value, value_value)
{
  var dest = document.getElementById("attributes");
  if (!dest)
    return;

  last_field_id_++;

  title = document.createElement('dt');
  input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('name', 'attributes[' + last_field_id_ + '][title]')
    input.setAttribute('value', title_value || "")
  title.appendChild(input)

  value = document.createElement('dd');
  input = document.createElement('input');
    input.setAttribute('type', 'text');
    input.setAttribute('name', 'attributes[' + last_field_id_ + '][value]')
    input.setAttribute('value', value_value || "")
  value.appendChild(input);

  dest.appendChild(title);
  dest.appendChild(value);
}
