<?php
/**
 * I provide completely working code within this article, which will not be
 * developed any further, because there are already existing packages, which try
 * to provide similar functionallities.
 */

namespace pdepend\reflection\parser;

require_once 'BaseTest.php';

/**
 * Test cases for the tokenizer class.
 *
 * @author  Manuel Pichler <mapi@pdepend.org>
 * @license Copyright by Manuel Pichler
 * @version $Revision$
 */
class TokenizerTest extends \pdepend\reflection\BaseTest
{
    /**
     * Test source code
     *
     * @var string
     */
    private $_source = '<?php
        class c
        {
            function x( self $x )
            {
                ?>}<?php
                $x = "

                " . " " .
                parent::x();
            }
        }';

    /**
     * The test tokenizer.
     *
     * @var \pdepend\reflection\parser\Tokenizer
     */
    private $_fixture = null;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_fixture = new Tokenizer( $this->_source );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\parser\ParserTokens
     * @covers \pdepend\reflection\parser\Tokenizer
     * @covers \pdepend\reflection\parser\Token
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testTokenizerScopeParsingWithNonPhpCurlyBrace()
    {
        $scope = 0;
        while ( ( $token = $this->_fixture->next() ) !== Tokenizer::EOF )
        {
            if ( $token->type === ParserTokens::T_SCOPE_CLOSE )
            {
                --$scope;
            }
            else if ( $token->type === ParserTokens::T_SCOPE_OPEN )
            {
                ++$scope;
            }
        }
        $this->assertSame( 0, $scope );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\parser\ParserTokens
     * @covers \pdepend\reflection\parser\Tokenizer
     * @covers \pdepend\reflection\parser\Token
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testTokenizerSetsCorrectStartLineNumbers()
    {
        $expected = array( 2, 2, 3, 4, 4, 4, 4, 4, 4, 5, 7, 7, 7, 9, 10, 10, 10, 10, 10, 10, 11, 12 );
        $actual   = array();

        while ( ( $token = $this->_fixture->next() ) !== Tokenizer::EOF )
        {
            $actual[] = $token->startLine;
        }

        $this->assertSame( $expected, $actual );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\parser\ParserTokens
     * @covers \pdepend\reflection\parser\Tokenizer
     * @covers \pdepend\reflection\parser\Token
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testTokenizerSetsCorrectEndLineNumbers()
    {
        $expected = array( 2, 2, 3, 4, 4, 4, 4, 4, 4, 5, 7, 7, 9, 9, 10, 10, 10, 10, 10, 10, 11, 12 );
        $actual   = array();

        while ( ( $token = $this->_fixture->next() ) !== Tokenizer::EOF )
        {
            $actual[] = $token->endLine;
        }

        $this->assertSame( $expected, $actual );
    }

    /**
     * @return void
     * @covers \pdepend\reflection\parser\ParserTokens
     * @covers \pdepend\reflection\parser\Tokenizer
     * @covers \pdepend\reflection\parser\Token
     * @group reflection
     * @group reflection::parser
     * @group unittest
     */
    public function testTokenizerTranslatesTStringSelfIntoTSelf()
    {
        $tokenType = 0;
        for ( $i = 0; $i < 7; ++$i )
        {
            $tokenType = $this->_fixture->next()->type;
        }
        $this->assertEquals( ParserTokens::T_SELF, $tokenType );
    }
}