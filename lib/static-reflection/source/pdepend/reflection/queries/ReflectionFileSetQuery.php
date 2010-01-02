<?php
/**
 * This file is part of the static reflection component.
 *
 * PHP Version 5
 *
 * Copyright (c) 2009-2010, Manuel Pichler <mapi@pdepend.org>,
 *                          Nils Adermann  <naderman@naderman.de>.
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
 * @category  PHP
 * @package   pdepend\reflection\queries
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @author    Nils Adermann <naderman@naderman.de>
 * @copyright 2009-2010 Manuel Pichler, Nils Adermann. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   SVN: $Id$
 * @link      http://pdepend.org/
 */

namespace pdepend\reflection\queries;

/**
 * This query class allows access to reflection class instances for all classes
 * and interfaces declared in the given files.
 *
 * <code>
 * $query   = $session->createDirectoryQuery();
 * $classes = $query->find( array(
 *                __DIR__ . '/source/A.php',
 *                __DIR__ . '/source/B.php'
 *            ) );
 *
 * foreach ( $classes as $class )
 * {
 *     echo 'Class: ', $class->getShortName(), PHP_EOL,
 *          'File:  ', $class->getFileName(), PHP_EOL,
 *          '-- ', PHP_EOL;
 * }
 * </code>
 *
 * @category  PHP
 * @package   pdepend\reflection\queries
 * @author    Manuel Pichler <mapi@pdepend.org>
 * @author    Nils Adermann <naderman@naderman.de>
 * @copyright 2009-2010 Manuel Pichler, Nils Adermann. All rights reserved.
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version   Release: @package_version@
 * @link      http://pdepend.org/
 */
class ReflectionFileSetQuery extends ReflectionQuery
{
    /**
     * The type of this class.
     */
    const TYPE = __CLASS__;

    /**
     * This method will create reflection class instances for all interfaces
     * and classes that can be found in the given source code files.
     *
     * @param string $pathnames The source directory that is the target of the
     *        class search.
     *
     * @return Iterator
     */
    public function find( array $paths )
    {
        $classes = array();

        foreach ( $paths as $path )
        {
            if ( file_exists( $path ) === false || !is_file( $path ) )
            {
                throw new \LogicException( 'Invalid or nonexistent file ' . $path );
            }
            $fileInfo = new \SplFileInfo($path);

            $fileClasses = $this->parseFile( $fileInfo->getRealpath() );
            $classes = array_merge( $classes, $fileClasses );
        }

        return new \ArrayIterator( $classes );
    }
}
