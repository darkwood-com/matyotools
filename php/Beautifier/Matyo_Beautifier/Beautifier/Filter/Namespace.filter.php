<?php

require_once 'FSM/FSM.php';

class PHP_Beautifier_Filter_Namespace extends PHP_Beautifier_Filter
{
    private $fsm = null;
    private $stack = array();

    public function __construct(PHP_Beautifier $oBeaut, $aSettings = array())
    {
        parent::__construct($oBeaut, $aSettings);

        //FSM that detect namespaces
        $this->fsm = new FSM('NONE', $this->stack);
        $this->fsm->addTransition('t_include', 'NONE', 'INCLUDE');
        $this->fsm->addTransition('t_whitespace', 'INCLUDE', 'WHITESPACE');
        $this->fsm->addTransition('t_string', 'WHITESPACE', 'STRING');
        $this->fsm->addTransition('t_semi_colon', 'STRING', 'NONE', array($this, 'fsm_end'));

        $this->fsm->addTransitionAny('NONE', 'NONE', array($this, 'fsm_reset'));
        $this->fsm->addTransitionAny('INCLUDE', 'NONE', array($this, 'fsm_reset'));
        $this->fsm->addTransitionAny('WHITESPACE', 'NONE', array($this, 'fsm_reset'));
        $this->fsm->addTransitionAny('STRING', 'NONE', array($this, 'fsm_reset'));

        //$this->fsm->addTransition('T_STRING', 'USE_NS_SEPARATOR', 'USE_NAMESPACE');
        //$this->fsm->addTransition('T_NS_SEPARATOR', 'USE_NAMESPACE', 'USE_NS_SEPARATOR');
    }

    public function fsm_reset($symbol, $stack)
    {
        $stack = array();
    }

    public function fsm_end($symbol, $stack)
    {
    }

    public function __call($sMethod, $aArgs)
    {
        if (!is_array($aArgs) or count($aArgs) != 1) {
            throw (new Exception('Call to Filter::__call with wrong argument'));
        }

        array_push($this->stack, $aArgs[0]);
        $this->fsm->process($sMethod);

        return PHP_Beautifier_Filter::BYPASS;
    }

    public function postProcess()
    {
        print_r($this->stack);
    }
}
?>
