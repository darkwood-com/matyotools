<?php
/**
 * Explode inpupt into lines
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class Explode implements Iterator
{
	/**
	 * current position in iterator
	 *
	 * @var int
	 */
	protected $_position;
	
	/**
	 * array of lines txt
	 *
	 * @var array<String>
	 */
	protected $_lines;
		
	/**
	 * array of rules for each line
	 *
	 * @var array<Rules>
	 */
	protected $_rulesLine;
	
	public function __construct($input = "", $separator = "")
	{
		$this->_position = 0;
		if(RegExp::isRegExp($separator))
		{
			$this->_lines = preg_split($separator, $input);
		}
		else
		{
			$this->_lines = explode($separator, $input);			
		}
		$this->_rulesLine = array();
	}
	
	public function current()
	{
		return $this->_lines[$this->_position];
	}
	
	public function key()
	{
		return $this->_position;
	}
	
	public function next()
	{
		$this->_position++;
	}
	
	public function rewind()
	{
		$this->position = 0;
	}
	
	public function valid()
	{
		if($this->_position >= count($this->_lines))
		{
			return false;
		}

		$currentLine = $this->current();
		
		//treat rules in order of apparition
		foreach($this->_rulesLine as $rule)
		{
			switch($rule->apply($currentLine))
			{
				case Rule::ALLOW:
					//valid line
					return true;
					
				case Rule::BAN:
					//skip line
					$this->next();
					return $this->valid();
					
				case Rule::PASS:
				default:
					//no rule
					break;
			}
		}
		
		return true;
	}
	
	/**
	 * add a new rule for treating line that will valid the line
	 *
	 * @param $rule simple or reg expression
	 */
	public function allowLines($rule)
	{
		$this->_rulesLine[] = new Rule($rule, Rule::ALLOW);
	}
	
	/**
	 * add a new rule for treating line that will skip the line
	 *
	 * @param $rule simple or reg expression
	 */
	public function banLines($rule)
	{
		$this->_rulesLine[] = new Rule($rule, Rule::BAN);		
	}
}
?>