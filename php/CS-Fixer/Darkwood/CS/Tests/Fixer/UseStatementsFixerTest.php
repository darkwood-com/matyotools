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
namespace Symfony\Component\Console;

use Foo\Bar;
use Foo\Bar\Baar\Baar;
use Foo\Bar\Baz;
use Foo\Bar\Foo as Fooo;
use Foo\Bar\FooBar as FooBaz;
use SomeClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\Helper\DialogHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();
EOF;

        $input = <<<'EOF'
namespace Symfony\Component\Console;

use Symfony\Component\Console\Input\InputInterface;
use Foo\Bar;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Foo\Bar\Baz;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Command\ListCommand;
use Foo\Bar\FooBar as FooBaz;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Foo\Bar\Foo as Fooo;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Helper\DialogHelper;
use Foo\Bar\Baar\Baar;
use Symfony\Component\Console\Helper\HelperSet;
use SomeClass;
use Symfony\Component\Console\Command\HelpCommand;

$a = new Bar();
$a = new FooBaz();
$a = new someclass();
EOF;

        $this->assertEquals($expected, $fixer->fix($file, $input));
    }
}
