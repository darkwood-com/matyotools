<?php
/**
 * Generic search system
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

abstract class Search
{
	/**
	 * input txt
	 *
	 * @var string
	 */
	protected $_input = "";
	
	/**
	 * search pattern
	 *
	 * @var string
	 */
	protected $_search = "";
	
	/**
	 * contructor
	 *
	 * @param $input input data
	 * @param $search pattern
	 * @param $replace pattern
	 */
	public function __construct($input = "", $search = "")
	{
		$this->_input = $input;
		$this->_search = $search;
	}
	
	/**
	 * search 
	 *
	 * @return if found : return 1, if not found return 0, if error return -1
	 */
	public function doSearch()
	{
		return -1;
	}
}
?>