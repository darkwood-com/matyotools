<?php
require 'config.inc';
require 'basecampClass.php';

function printr($a)
{
	echo '<xmp>';
	print_r($a);
	echo '</xmp>';
}

$session = new basecampClass($config);

//args input
if(isset($argc))
{
	//command line input
	$mode = 'sh';
	$input = $argv;
	
} else {
	//http input
	$mode = 'http';
	$input = $_SERVER['REQUEST_URI'];
	if(preg_match('/^\/([0-9a-zA-Z])+\.([0-9a-zA-Z])+$/', $input)) exit(0); //file url
}

$args = $session->unserializeArgs($input, $mode);

$lines = array();

//log file management
if(is_numeric($args['mode']))
{
	$logs = $session->logList();
	if(isset($logs[intval($args['mode'])]))
	{
		//override args from log
		$args = $logs[intval($args['mode'])];
		$lines[] = 'arguments from log : '.implode(' ', $args);
	}
	else exit(0);
}
$session->logAdd($args);

//exec
$linesExec = $session->exec($args, $mode);
foreach($linesExec as $lineExec)
{
	$lines[] = $lineExec;
}

$lines[] = '';

//display
switch ($mode) {
    default:
    case 'text/plain':
    case 'http':
    	header('Content-type: text/plain; charset=text/plain');
    case 'sh':
		echo implode("\n", $lines);
        break;
    case 'html':
    	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>Basecamp</title>
</head>
<body>
'.implode('<br />', $lines).'
</body>
</html>';
        break;
}
?>
