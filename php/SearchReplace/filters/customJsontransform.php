<?php
include_once __dir__."/../run.php";

use TokenReflection\Broker;

/**
 * add getter and setters to class
 */
class SearchReplace extends Replace 
{
    public function doReplace()
    {
        $output = parent::doReplace();

        $output = json_decode($output);
        $output = json_encode($output);
        $output = addslashes($output);
        $output = 'curl --data "input='.$output.'" http://46.218.70.155/app_dev.php/contents/getList';

        $this->_output = $output;
        return $output;
    }
}

run(__FILE__);
?>