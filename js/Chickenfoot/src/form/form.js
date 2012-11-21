/**
 * Actions on Formulary
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Form.Form = Class.create();
Form.Form.prototype = Object.extend(new Core.ChickenFoot(), {
	/**
	 * @post contructor
	 */
	initialize: function() {
		
	},
	
	/**
	 * @post fill form with random values
	 */
	fill: function()
	{
		var oMatchesPtr = this.find(Pattern.TEXTBOX);
		
		while (oMatchesPtr.hasMatch) {
			//generate random text
			var sRandomText = "";
			var sAlphaNum = "abcdefghijklmnopqrstuvwxyz0123456789";			
			for (var i = 1; i < 10; i++) {
				sRandomText += sAlphaNum.charAt(Math.floor(Math.random() * sAlphaNum.length));
			}
			
			this.enter(oMatchesPtr, sRandomText);
			oMatchesPtr = oMatchesPtr.next;
		}
	}
});