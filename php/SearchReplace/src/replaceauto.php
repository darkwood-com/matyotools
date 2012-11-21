<?php
/**
 * Automaticaly detemine replace process
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class ReplaceAuto extends Replace
{
	public function doReplace()
	{
		if(RegExp::isRegExp($this->_search))
		{
			//preg expression
			$replacePreg = new ReplacePreg($this->_input, $this->_search, $this->_replace);
			return $replacePreg->doReplace();
		}
		else
		{
			//simple expression
			$replacePreg = new ReplaceSimple($this->_input, $this->_search, $this->_replace);
			return $replacePreg->doReplace();
		}
	}
}
?>