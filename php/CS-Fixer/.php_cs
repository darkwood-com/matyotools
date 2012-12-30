<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('.php_cs')
    ->notName('composer.*')
    ->notName('phpunit.xml*')
    ->notName('*.phar')
    ->exclude('vendor')
    ->exclude('Darkwood/CS/Tests/Fixer')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->finder($finder)
;

