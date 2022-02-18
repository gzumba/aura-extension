<?php
namespace Zumba\Tests\Aura\Parser;

use PHPUnit\Framework\TestCase;
use Zumba\Aura\Parser\PgsqlParser;

class PgsqlParserTest extends TestCase
{
    protected function setUp(): void
    {
        $this->parser = new PgsqlParser();
    }

    public function testUnicodeDoubleQuotedIdentifier()
    {
        $parameters = ['a000' => ['foo', 'bar']];
        $sql = <<<SQL
SELECT U&"\a000"
FROM (SELECT 1 AS U&":a000" UEScAPE ':') AS temp
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testCStyleStringConstants()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT E'C-style escaping \' :foo \''
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<<SQL
SELECT E'Multiline'
       'C-style escaping \' :foo \' :foo'
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testDollarQuotedStrings()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = 'SELECT $$:foo$$';
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = 'SELECT $tag$ :foo $tag$';
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = 'SELECT $outer$ nested strings $inner$:foo$inner$ $outer$';
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = 'SELECT $€$hello$€$';
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = 'SELECT $€$hello$€';
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testTypeCasting()
    {
        $parameters = ['TEXT' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT 'hello'::TEXT
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testArrayAccessor()
    {
        $parameters = ['2' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT test[1:2]
FROM (
SELECT CAST('{"foo", "bar", "baz", "qux"}' AS TEXT[]) AS test
) AS t
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testInvalidPlaceholderName()
    {
        $parameters = [']' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT 'hello':]
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    protected function rebuild($sql, $parameters)
    {
        $parser = clone $this->parser;
        return $parser->rebuild($sql, $parameters);
    }

    public function testReplaceMultipleUsesOfNamedParameter()
    {
        $parameters = ['foo' => 'bar'];
        $sql = "SELECT :foo AS a, :foo AS b";
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $expectedStatement = "SELECT :foo AS a, :foo__1 AS b";
        $expectedValues = ['foo' => 'bar', 'foo__1' => 'bar'];
        $this->assertEquals($expectedStatement, $statement);
        $this->assertEquals($expectedValues, $values);
    }

    public function testReplaceNumberedParameter()
    {
        $parameters = ['bar', 'baz', null];
        $sql = "SELECT ? AS a, ? AS b FROM table WHERE id = ?";
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $expectedStatement = "SELECT :__1 AS a, :__2 AS b FROM table WHERE id = :__3";
        $expectedValues = ['__1' => 'bar', '__2' => 'baz', '__3' => null];
        $this->assertEquals($expectedStatement, $statement);
        $this->assertEquals($expectedValues, $values);
    }

    public function testReplaceArrayAsParameter()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = "SELECT :foo";
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $expectedStatement = "SELECT :foo_0, :foo_1";
        $expectedValues = ['foo_0' => 'bar', 'foo_1' => 'baz'];
        $this->assertEquals($expectedStatement, $statement);
        $this->assertEquals($expectedValues, $values);

        $parameters = [['bar', 'baz']];
        $sql = "SELECT ?";
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $expectedStatement = "SELECT :__1, :__2";
        $expectedValues = ['__1' => 'bar', '__2' => 'baz'];
        $this->assertEquals($expectedStatement, $statement);
        $this->assertEquals($expectedValues, $values);
    }

    public function testDoubleQuotedIdentifier()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT ":foo"
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<<SQL
SELECT "to use double quotes, just double them "" :foo "
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testStringConstants()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT ':foo'
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);


        $sql = <<<SQL
SELECT 'single quote''s :foo'
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<<SQL
SELECT 'multi line string'
':foo'
'bar'
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testEscapedCharactersInStringConstants()
    {
        $parameters = ['foo' => ['bar', 'baz']];
        $sql = <<<SQL
SELECT 'Escaping \' :foo \''
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<< 'SQL'
SELECT "Escaping \" :foo \""
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

//         $sql = <<<SQL
// SELECT "Escaping \\\\" :foo ""
// SQL;
//         $expectedStatement = <<<SQL
// SELECT "Escaping \\\\" :foo, :foo_0 ""
// SQL;
//         $expectedValues = ['foo' => 'bar', 'foo_0' => 'baz'];
//         list ($statement, $values) = $this->rebuild($sql, $parameters);
//         $this->assertEquals($expectedStatement, $statement);
//         $this->assertEquals($expectedValues, $values);

        $sql = <<<SQL
SELECT "Escaping \" :foo \"
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<<SQL
SELECT "Escaping "" :foo """
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);

        $sql = <<<SQL
SELECT 'Escaping '' :foo '''
SQL;
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($sql, $statement);
    }

    public function testIssue107()
    {
        $sql = "UPDATE table SET `value`=:value, `blank`='', `value2` = :value2, `blank2` = '', `value3`=:value3 WHERE id = :id";
        $parameters = ['value'=> 'string', 'id'=> 1, 'value2'=> 'string', 'value3'=>'string'];
        list ($statement, $values) = $this->rebuild($sql, $parameters);
        $this->assertEquals($statement, $sql);
        $this->assertEquals($values, $parameters);
    }

    public function testCastingWorks(): void
    {
        $sql = "SELECT * FROM table WHERE :value::timestamp > '2021-1-1'";

        list ($statement, $values) = $this->rebuild($sql, ['value' => '2022-1-1']);
        $this->assertEquals($statement, $sql);
    }
}
