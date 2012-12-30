<?php

namespace Darkwood\CS;

use Symfony\CS\Fixer as BaseFixer;
use Symfony\Component\Finder\Finder;

class Fixer extends BaseFixer
{
    const VERSION = '0.2';

    public function registerBuiltInFixers()
    {
        parent::registerBuiltInFixers();

        foreach (Finder::create()->files()->in(__DIR__.'/Fixer') as $file) {
            $class = 'Darkwood\\CS\\Fixer\\'.basename($file, '.php');
            $this->addFixer(new $class());
        }
    }
}
