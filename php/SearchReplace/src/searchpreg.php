<?php
/**
 * Regular search expression
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class SearchPreg extends Search
{
	public function __construct($input = "", $search = "")
	{
		$this->_input = $input;
		$this->_search = $search;
	}
	
	public function doSearch(&$matches = "")
	{
		if(preg_match($this->_search, $this->_input, $matches) > 0)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}
}
?>