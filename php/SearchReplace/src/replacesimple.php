<?php
/**
 * Simple replace expression
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class ReplaceSimple extends Replace
{
	public function doReplace()
	{
		$this->_output = str_replace($this->_search, $this->_replace, $this->_input);
		
		return $this->_output;
	}
}
?>