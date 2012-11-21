<?php
/**
 * Simple search expression
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class SearchSimple extends Search
{
	public function doSearch()
	{
		return ($this->_input == $this->_search);
	}
}
?>