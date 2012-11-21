/**
 * Url basics
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Core.Url = Class.create();
Core.Url.prototype = {
	/**
	 * @post contructor
	 */
	initialize: function() {
		//String url root path
		this.m_sUrl = "http://" + location.host;
	},
	
	/**
	 * @post contructor
	 * @param String sUrl is the url after host url name
	 * @param bool bIsAbsolute (optional)[default = false, true]
	 */
	initialize: function(sUrl, bIsAbsolute) {
		if(bIsAbsolute==undefined) {
			bIsAbsolute = false;
		}
		
		//String url root path
		if(bIsAbsolute) {
			this.m_sUrl = sUrl;			
		} else {
			this.m_sUrl = "http://" + location.host + sUrl;			
		}
	},
	
	/**
	 * @post add
	 * @param String sUrl is the url after host url name
	 * @return Core.Url contatenation of this.m_sUrl and m_sUrl
	 */
	add: function(sUrl) {
		var oUrl = new Core.Url();
		oUrl.m_sUrl = this.m_sUrl + sUrl;
	
		return oUrl;
	},

	/**
	 * @post return url path
	 * @return String this.m_sUrl
	 */
	get: function() {
		return this.m_sUrl;
	}
};