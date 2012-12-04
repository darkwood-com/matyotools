<?php
include "PHP_Beautifier/Beautifier.php";

$file = 'test';

$b = new PHP_Beautifier();
$b->setIndentChar(' ');
$b->setIndentNumber(4);
$b->setNewLine("\n");

$b->setInputFile($file.'.php');
$b->setOutputFile(dirname(__FILE__).'/'.$file.'.beautified.php');
$b->startLog(dirname(__FILE__).'/'.$file.'.beautifier.log');

$b->addFilterDirectory(dirname(__FILE__) . '/Matyo_Beautifier/Beautifier/Filter');
$b->addFilter('None');
$b->addFilter('Namespace');

$b->process();

//display before and after
$txt = array(
    file_get_contents('test.php'),
    '',
    '',
    '************************************************************************',
    '',
    '',
    $b->get(),
);
echo '<xmp>'.implode("\n", $txt).'</xmp>';

$b->save();
