<?php
/**
 * Rule when using Explode
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class Rule
{
	const PASS = 1;
	const ALLOW = 2;
	const BAN = 3;
	
	protected $_rule;
	protected $_type;
	
	public function __construct($rule = "", $type = Rule::PASS)
	{
		$this->_rule = $rule;
		$this->_type = $type;
	}
	
	/**
	 * apply rule on string and return the rule statut
	 *
	 * @param string $input
	 * @return statut type
	 */
	public function apply($input)
	{
		$searchPattern = new SearchAuto($input, $this->_rule);
		if($searchPattern->doSearch())
		{
			return $this->_type;
		}
				
		return Rule::PASS;
	}
}
?>