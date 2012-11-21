<?php
/**
 * Regular Expression
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

class RegExp
{
	static public function isRegExp($expr)
	{
		$exprLength = strlen($expr);
		if($exprLength > 2 && $expr[0] == '/' && $expr[$exprLength - 1] == '/')
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
?>