<?php
/**
 * Regular replace expression
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class ReplacePreg extends Replace
{
	/**
	 * specify custom replacement for specific args
	 *
	 * @var array<int => array<string => string>>
	 */
	protected $_replacementArgs = array();
	
	protected function _doReplaceArg($inputPrefix, $outputPrefix)
	{
		foreach($this->_replacementArgs as $indexArg => $replacementArg)
		{
			$replaceArgsInReplace = new ReplaceSimple($this->_replace, $inputPrefix.$indexArg, $outputPrefix.$indexArg);
			$this->_replace = $replaceArgsInReplace->doReplace();
		}
	}
	
	public function doReplace()
	{
		$searchPreg = new SearchPreg($this->_input, $this->_search);
		if($searchPreg->doSearch($matcheArgs))
		{
			$replace = $this->_replace;
			
			//replace \i => @i in $this->_replace pattern, that will disable regex doReplace for process args
			$this->_doReplaceArg("\\", "@");
			
			//make regex doReplace
			$this->_output = preg_replace($this->_search, $this->_replace, $this->_input);
			
			//process args, replace @i with the good values
			foreach($this->_replacementArgs as $indexArg => $replacementArg)
			{
				$matcheArg = $matcheArgs[$indexArg];
				if(isset($matcheArg))
				{
					foreach($replacementArg as $searchArg => $replaceArg)
					{
						if($matcheArg == $searchArg)
						{
							$replaceProcessArg = new ReplaceSimple($this->_output, "@".$indexArg, $replaceArg);
							$this->_output = $replaceProcessArg->doReplace();
						}
					}
				}
			}
			
			//replace @i => \i in $this->_replace pattern, to get default replace value
			$this->_doReplaceArg("@", "\\");
		}
		
		return $this->_output;
	}
	
	/**
	 * add a new custom replacement for a specific arg
	 *
	 * @param int $indexArg
	 * @param array $replacementArg replacement case
	 */
	public function processArg($indexArg, $replacementArg)
	{
		$this->_replacementArgs[$indexArg] = $replacementArg;
	}
}
?>