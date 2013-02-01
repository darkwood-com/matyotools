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
                $annotations = $property->getAnnotations();

                $type = isset($annotations['var'], $annotations['var'][0]) ? ' '.$annotations['var'][0] : '';

                $output[] = array(
                    '/**',
                    ' * @return'.$type,
                    ' */',
                    'public function get'.ucfirst($propertyName).'()',
                    '{',
                    '    return $this->'.$propertyName.';',
                    '}',
                    '',
                    '/**',
                    ' * @param'.$type.' $'.$propertyName,
                    ' * @return '.$class->getName(),
                    ' */',
                    'public function set'.ucfirst($propertyName).'($'.$propertyName.')',
                    '{',
                    '    $this->'.$propertyName.' = $'.$propertyName.';',
                    '',
                    '    return $this;',
                    '}',
                );

                $i = 0;
            }
        }

        foreach($output as $k => $v) {
            $output[$k] = implode("\n", $v);
        }
        $output = implode("\n\n", $output);

        $this->_output = $output;
        return $output;
    }
}

run(__FILE__);
?>