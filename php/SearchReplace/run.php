<?php
require 'vendor/autoload.php';

require_once "src/rule.php";
require_once "src/regexp.php";

require_once "src/search.php";
require_once "src/searchpreg.php";
require_once "src/searchsimple.php";
require_once "src/searchauto.php";

require_once "src/replace.php";
require_once "src/replacepreg.php";
require_once "src/replacesimple.php";
require_once "src/replaceauto.php";

require_once "src/explode.php";
require_once "src/explodeinlines.php";


function file_get_contents_utf8($fn) {
     $content = file_get_contents($fn);
     return mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
} 

function run($file) {
	if(!isset($file))
	{
		$argc = 2;
		$argv[1] = "C:\\Documents and Settings\\Mathieu\\Mes documents\\work\\Tests\\fields_sr.php";
		
		if ($argc != 2)
		{
			die("Syntaxe: SearchReplace <nom_fichier>\n");
		}
		$file = $argv[1];
		echo "SearchReplace : ".$file."\n";
		if (!is_file($file))
		{
			die("Nom de fichier incorrect\n");
		}
	}
	
	try
	{
        $dir = __DIR__;
        $filename = basename($file, '.php');

		//input
		$inputFile = $dir.'/tests/'.$filename.".input.txt";
		$inputText = "";
        if(isset($_POST['input'])) {
            $inputText = $_POST['input'];
        } else if (file_exists($inputFile)) {
			$inputText = file_get_contents_utf8($inputFile);
		}
		
		//search and replace
		include_once $file;
		$sr = new SearchReplace($inputText);
		$outputText = $sr->doReplace();
		
		//output
		$outputFile = $dir.'/tests/'.$filename.".output.txt";
		file_put_contents($outputFile, $outputText);


        echo '
        <html>
            <head>

            </head>
            <body>
            <form method="POST">
            <textarea cols="60" rows="80" name="input">'.$inputText.'</textarea>
            <textarea cols="60" rows="80" name="output">'.$outputText.'</textarea>
            <br /><input type="submit" />
            </form>
            </body>
        </html>
        ';
	}
	catch (Exception $e)
	{
		echo $e;
	}
}
?>