/**
 * Xpath basics
 *
 * @see parent class on : srcChickenfoot/xpcom/Xpath.js
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Core.XPath = Class.create();
Core.XPath.prototype = Object.extend(new XPath(), {
	/**
	 * @post contructor
	 * @param String sPath is the xpath string representation
	 */
	initialize: function(sPath) {
		this._xpathExpression = sPath;
		this._namespaceResolver = null;
		this._resultType = XPathResult.ANY_TYPE;
	},
	
	/**
	 * @post return a new Core.XPath concatenation of this + sXPath
	 * @param String sXPath
	 * @return Core.XPath the concatenation
	 */
	add: function(sXPath) {
		return new Core.XPath(this.xpathExpression + sXPath);
	}
});