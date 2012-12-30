<?php

namespace Darkwood\CS\Tests\Fixer;

use Darkwood\CS\Fixer\UseStatementsFixer;

class UseStatementsFixerTest extends \PHPUnit_Framework_TestCase
{
    public function testFix()
    {
        $fixer = new UseStatementsFixer();
        $file = new \SplFileInfo(__FILE__);

        $expected = <<<'EOF'
use Foo\Bar;
use Foo\Bar\FooBar as FooBaz;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();
EOF;

        $input = <<<'EOF'
use Foo\Bar;
use Foo\Bar\Baz;
use Foo\Bar\FooBar as FooBaz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\Baar\Baar;
use SomeClass;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
