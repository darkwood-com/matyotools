<?php
/**
 * Explode inpupt into lines
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class ExplodeInLines extends Explode
{
	/**
	 * trim each line
	 *
	 * @var bollean
	 */
	protected $_isTrimLine;
		
	public function __construct($input = "")
	{
		parent::__construct($input, "\n");
		
		//remplace empty line ("\r") by ""
		foreach($this->_lines as $lineID => $lineVal)
		{
			$searchEmptyLine = new SearchAuto($lineVal, "\r");
			if($searchEmptyLine->doSearch())
			{
				$this->_lines[$lineID] = "";
			}
		}
	}
	
	public function trimLines($isTrimLine)
	{
		$this->_isTrimLine = $isTrimLine;
	}
	
	public function skipBlankLines($isSkipBlankLines)
	{
		if($isSkipBlankLines == true)
		{
			$this->banLines("");
		}
	}
	
	public function current()
	{
		$line = parent::current();
		if($this->_isTrimLine)
		{
			$line = trim($line);
		}
		
		return $line;
	}
}
?>