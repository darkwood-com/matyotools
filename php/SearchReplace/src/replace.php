<?php
/**
 * Generic replace system
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

abstract class Replace
{
	/**
	 * input txt
	 *
	 * @var string
	 */
	protected $_input = "";
		
	/**
	 * output txt
	 *
	 * @var string
	 */
	protected $_output = "";

	/**
	 * search pattern
	 *
	 * @var string
	 */
	protected $_search = "";

	/**
	 * replace pattern
	 *
	 * @var string
	 */
	protected $_replace = "";
	
	/**
	 * contructor
	 *
	 * @param $input input data
	 * @param $search pattern
	 * @param $replace pattern
	 */
	public function __construct($input = "", $search = "", $replace = "")
	{
		$this->_input = $input;
		$this->_search = $search;
		$this->_replace = $replace;
	}
	
	/**
	 * doReplace input into output
	 *
	 * @return the replaced output
	 */
	public function doReplace()
	{
		$this->_output = $this->_input;
		
		return $this->_output;
	}
}
?>