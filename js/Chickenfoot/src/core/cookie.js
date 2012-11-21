/**
 * Cookie basics
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Core.Cookie = Class.create();
Core.Cookie.prototype = {
	/**
	 * @post contructor
	 */
	initialize: function() {
	},
	
	/**
     * set a cookie
	 * @name String name
	 * @value String sCookie
	 * @days int set day expiration (day to remove)
	 * @path String cookie path
	 */
	set: function(name,value,days,path) {
		if(path == undefined)
		{
			path = '/';
		}
		
		if (days != undefined) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path="+path;
        output(name+"="+value+expires+"; path="+path);
	 },
	 
	/**
     * get a cookie
	 * @post return cookie value
	 * @return String
	 */
	get: function(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i < ca.length; i++) {
			var c = ca[i];
            c = c.replace(/^\s+/g,''); //trim left
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	},
    
    /**
     * get all cookies
     * @return Array
     */
    all: function() {
        var cookie = {};
        var ca = document.cookie.split(';');
        for(var i=0; i < ca.length; i++) {
            var c = ca[i];
            c = c.replace(/^\s+/g,'').replace(/\s+$/g,'');
            c = c.split('=');
            cookie[c[0]] = c[1];
        }
        
        return cookie;
    },
    
    /**
     * delete a cookie
	 * @name String name
	 */
    delete: function(name) {
        this.set(name, '', -1);
    },
    
    /**
     * delete all cookies
	 */
    clear: function() {
        var cookies = this.all();
        for (var key in cookies) {
            this.delete(key);
        }
    }
};