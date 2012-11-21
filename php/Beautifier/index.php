<?php
include "PHP_Beautifier/Beautifier.php";

$b = new PHP_Beautifier();
$b->removeFilter('Default');
$b->addFilterDirectory(dirname(__FILE__) . '/Filter');
$b->addFilter('None');

$b->setIndentChar(' ');
$b->setIndentNumber(4);
$b->setNewLine("\n");

$b->setInputFile('test.php');
$b->setOutputFile(dirname(__FILE__).'/test.beautified.php');
$b->startLog(dirname(__FILE__).'/php_beautifier.log');

$b->process();

$b->show();

$b->save();
