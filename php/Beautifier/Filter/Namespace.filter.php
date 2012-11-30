<?php
class PHP_Beautifier_Filter_Namespace extends PHP_Beautifier_Filter
{
    private $tag = null;

    public function t_include($tag)
    {
        $this->tag = $this->oBeaut->iCount;

        return PHP_Beautifier_Filter::BYPASS;
    }

    public function t_ns_separator($tag)
    {
        return PHP_Beautifier_Filter::BYPASS;
    }

    public function postProcess()
    {
        print_r($this->oBeaut->getTokenAssoc($this->tag));
        print_r($this->oBeaut->getTokenAssocText($this->tag));
    }
}
?>
