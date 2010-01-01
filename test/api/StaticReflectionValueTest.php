<?php
/**
 * This file is part of the static reflection component.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@pdepend.org>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Manuel Pichler nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category  StaticAnalysis
 * @package   pdepend\reflection\api
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

namespace pdepend\reflection\api;

require_once 'BaseTest.php';

/**
 * Test cases for the reflection value class.
 *
 * @category  StaticAnalysis
 * @package   pdepend\reflection\api
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @copyright 2009-2010 Manuel Pichler. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class StaticReflectionValueTest extends \pdepend\reflection\BaseTest
{
    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForNull()
    {
        $value = new StaticReflectionValue( null );
        $this->assertEquals( 'NULL', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForArray()
    {
        $value = new StaticReflectionValue( array() );
        $this->assertEquals( 'Array', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForBooleanTrue()
    {
        $value = new StaticReflectionValue( true );
        $this->assertEquals( 'true', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForBooleanFalse()
    {
        $value = new StaticReflectionValue( false );
        $this->assertEquals( 'false', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForFloat()
    {
        $value = new StaticReflectionValue( 3.14 );
        $this->assertEquals( '3.14', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForInteger()
    {
        $value = new StaticReflectionValue( 42 );
        $this->assertEquals( '42', $value->__toString() );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\api\StaticReflectionValue
     * @group reflection
     * @group reflection::api
     * @group unittest
     */
    public function testToStringForString()
    {
        $value = new StaticReflectionValue( 'Hello World' );
        $this->assertEquals( "'Hello World'", $value->__toString() );
    }
}
