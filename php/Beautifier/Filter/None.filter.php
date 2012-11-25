<?php
class PHP_Beautifier_Filter_None extends PHP_Beautifier_Filter
{
    protected $sDescription = 'None Filter for PHP_Beautifier';
    function __call($sMethod, $aArgs)
    {
        if (!is_array($aArgs) or count($aArgs) != 1) {
            throw (new Exception('Call to Filter::__call with wrong argument'));
        }
        PHP_Beautifier_Common::getLog()->log('None Filter:unhandled[' . $aArgs[0] . ']', PEAR_LOG_DEBUG);
        $this->oBeaut->add($aArgs[0]);
    }
}
?>
