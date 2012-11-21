<?php
/**
 * Automaticaly detemine search process
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class SearchAuto extends Search
{
	public function doSearch()
	{
		if(RegExp::isRegExp($this->_search))
		{
			//preg expression
			$searchPreg = new SearchPreg($this->_input, $this->_search);
			return $searchPreg->doSearch();
		}
		else
		{
			//simple expression
			$searchSimple = new SearchSimple($this->_input, $this->_search);
			return $searchSimple->doSearch();
		}
	}
}
?>