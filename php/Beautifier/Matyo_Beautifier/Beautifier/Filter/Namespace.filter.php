<?php

require_once 'FSM/FSM.php';

class PHP_Beautifier_Filter_Namespace extends PHP_Beautifier_Filter
{
    private $fsm = null;

    public function __construct(PHP_Beautifier $oBeaut, $aSettings = array())
    {
        parent::__construct($oBeaut, $aSettings);

        //FSM that detect namespaces
        $stack = array();
        $this->fsm = new FSM('start', $stack);
    }

    public function __call($sMethod, $aArgs)
    {
        if (!is_array($aArgs) or count($aArgs) != 1) {
            throw (new Exception('Call to Filter::__call with wrong argument'));
        }

        return PHP_Beautifier_Filter::BYPASS;
    }

    public function t_include($tag)
    {
        return PHP_Beautifier_Filter::BYPASS;
    }

    public function t_ns_separator($tag)
    {
        return PHP_Beautifier_Filter::BYPASS;
    }

    public function postProcess()
    {

    }
}
?>
