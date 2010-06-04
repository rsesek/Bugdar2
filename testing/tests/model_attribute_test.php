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

require_once BUGDAR_ROOT . '/includes/model_attribute.php';

class ModelAttributeTest extends BugdarTestCase
{
    public function testValidateTextEmtpyNoDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_TEXT;
        $attr->required = TRUE;

        $v = $attr->Validate('   ');
        $this->assertFalse($v[0]);

        $v = $attr->Validate('');
        $this->assertFalse($v[0]);
    }

    public function testValidateTextEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_TEXT;
        $attr->required = TRUE;
        $attr->default_value = 'Cows';

        $v = $attr->Validate('  ');
        $this->assertTrue($v[0]);
        $this->assertEquals('Cows', $v[1]);
    }

    public function testValidateNotRequiredTextEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_TEXT;
        $attr->required = FALSE;
        $attr->default_value = 'Bears';

        $v = $attr->Validate('');
        $this->assertTrue($v[0]);
        $this->assertEquals('Bears', $v[1]);
    }

    public function testValidateTextRegex()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_TEXT;
        $attr->validator_pattern = 'Mo{2,}';

        $v = $attr->Validate('Mo');
        $this->assertFalse($v[0]);
        $this->assertEquals('Mo', $v[1]);

        $v = $attr->Validate('Moooo');
        $this->assertTrue($v[0]);

        $v = $attr->Validate('Mooooooooooo');
        $this->assertTrue($v[0]);
    }

    public function testValidateBooleanTrue()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_BOOL;

        $v = $attr->Validate('tRUe');
        $this->assertTrue($v[0]);
        $this->assertSame(TRUE, $v[1]);

        $v = $attr->Validate('yEs');
        $this->assertTrue($v[0]);
        $this->assertSame(TRUE, $v[1]);

        $v = $attr->Validate('1');
        $this->assertTrue($v[0]);
        $this->assertSame(TRUE, $v[1]);
    }

    public function testValidateBooleanFalse()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_BOOL;

        $v = $attr->Validate('FaLsE');
        $this->assertTrue($v[0]);
        $this->assertSame(FALSE, $v[1]);

        $v = $attr->Validate('nO');
        $this->assertTrue($v[0]);
        $this->assertSame(FALSE, $v[1]);

        $v = $attr->Validate('0');
        $this->assertTrue($v[0]);
        $this->assertSame(FALSE, $v[1]);
    }

    public function testValidateBooleanDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_BOOL;
        $attr->default_value = TRUE;

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertSame(TRUE, $v[1]);

        $v = $attr->Validate('false');
        $this->assertTrue($v[0]);
        $this->assertSame(FALSE, $v[1]);
    }

    public function testValidateList()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_LIST;
        $attr->SetListOptions(array(
            'Red',
            'Green',
            'Blue'
        ));

        $v = $attr->Validate('Red');
        $this->assertTrue($v[0]);
        $this->assertEquals('Red', $v[1]);

        $v = $attr->Validate(' Green ');
        $this->assertTrue($v[0]);
        $this->assertEquals('Green', $v[1]);

        $v = $attr->Validate('bluE');
        $this->assertTrue($v[0]);
        $this->assertEquals('Blue', $v[1]);

        $v = $attr->Validate('Orange');
        $this->assertFalse($v[0]);
        $this->assertEquals('Orange', $v[1]);
    }

    public function testValidateListRequiredEmptyNoDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_LIST;
        $attr->required = TRUE;
        $attr->SetListOptions(array('R', 'G', 'B'));

        $v = $attr->Validate('');
        $this->assertFalse($v[0]);
    }

    public function testValidateListRequiredEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_LIST;
        $attr->required = TRUE;
        $attr->default_value = 'Undefined';
        $attr->SetListOptions(array('A', 'b', 'C'));

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertEquals('Undefined', $v[1]);
    }

    public function testValidateListNotRequiredEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_LIST;
        $attr->required = FALSE;
        $attr->default_value = 'Undefined';
        $attr->SetListOptions(array('A', 'b', 'C'));

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertEquals('Undefined', $v[1]);
    }

    public function testValidateDate()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_DATE;

        $v = $attr->Validate('January 1 2000');
        $this->assertTrue($v[0]);
        $this->assertGreaterThanOrEqual(gmmktime(0, 0, 0, 1, 1, 2000), $v[1]);

        $s = 'gobbledygoook';
        $v = $attr->Validate($s);
        $this->assertFalse($v[0]);
        $this->assertEquals($s, $v[1]);
    }

    public function testValidateDateDefault()
    {
        $attr = new Attribute();
        $attr->default_value = TRUE;
        $attr->type = Attribute::TYPE_DATE;

        $now = time();

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertGreaterThanOrEqual($time, $v[1]);

        $attr->required = TRUE;
        $v = $attr->Validate('');
        $this->assertTrue($v[0]);
        $this->assertGreaterThanOrEqual($time, $v[1]);
    }

    public function testValidateUserRequiredEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_USER;
        $attr->default_value = 42;
        $attr->required = TRUE;

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertEquals(42, $v[1]);
    }

    public function testValidateUserEmptyDefault()
    {
        $attr = new Attribute();
        $attr->type = Attribute::TYPE_USER;
        $attr->default_value = 42;
        $attr->required = FALSE;

        $v = $attr->Validate(NULL);
        $this->assertTrue($v[0]);
        $this->assertEquals(42, $v[1]);
    }

    public function testValidateUser()
    {
        $user = new User();
        $user->alias = 'fluffy@bluestatic.org';
        $user->email = 'fluffy@bluestatic.org';
        $user->password = 'abc123';
        $user->Insert();

        $attr = new Attribute();
        $attr->type = Attribute::TYPE_USER;

        $v = $attr->Validate('fluffy@bluestatic.org');
        $this->assertTrue($v[0]);
        $this->assertEquals($user->user_id, $v[1]);
    }
}
