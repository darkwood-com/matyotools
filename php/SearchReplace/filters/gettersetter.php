<?php
include_once __dir__."/../run.php";

use TokenReflection\Broker;

/**
 * add getter and setters to class
 */
class SearchReplace extends Replace 
{
    private function camelize($value, $lcfirst = true)
    {
        return preg_replace("/([_-\s]?([a-z0-9]+))/e", "ucwords('\\2')", $value);
    }

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
                    'public function get'.$this->camelize($propertyName).'()',
                    '{',
                    '    return $this->'.$propertyName.';',
                    '}',
                    '',
                    '/**',
                    ' * @param'.$type.' $'.$propertyName,
                    ' * @return '.$class->getName(),
                    ' */',
                    'public function set'.$this->camelize($propertyName).'($'.$propertyName.')',
                    '{',
                    '    $this->'.$propertyName.' = $'.$propertyName.';',
                    '',
                    '    return $this;',
                    '}',
                );
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