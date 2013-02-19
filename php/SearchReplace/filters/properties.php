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

        $broker = new Broker(new Broker\Backend\Memory());
        $broker->processString($output, 'output');

        $classes = $broker->getClasses();

        $output = array();

        foreach($classes as $class) {
            $properties = $class->getProperties();
            foreach($properties as $property) {
                $propertyName = $property->getName();
                $output[] = $property->getName();
            }
        }

        $output = implode("\n", $output);

        $this->_output = $output;
        return $output;
    }
}

run(__FILE__);
?>