<?php

namespace Darkwood\CS\Console;

use Darkwood\CS\Fixer;
use Symfony\CS\Console\Command\CompileCommand;
use Symfony\CS\Console\Command\FixCommand;
use Symfony\CS\Console\Command\ReadmeCommand;
use Symfony\CS\Console\Command\SelfUpdateCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('PHP CS Fixer', Fixer::VERSION);

        $this->add(new FixCommand(new Fixer()));
        $this->add(new CompileCommand());
        $this->add(new ReadmeCommand());
        $this->add(new SelfUpdateCommand());
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Mathieu Ledru</comment>';
    }
}
