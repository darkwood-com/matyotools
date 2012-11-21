/**
 * Chickenfoot basics
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

Core.ChickenFoot = Class.create();
Core.ChickenFoot.prototype = {
	/**
	 * @post contructor
	 */
	initialize: function() {
		//Float sleep time in seconds
		this.m_fSleepTime = 0.5;
		
		//mode ['action', 'silence']
		this.m_sMode = 'action'; //internal use
	},
	
	/**
	 * @post output text in the console
	 * @param String sArgs is the output text in the console
	 */
	output: function(sArgs) {
		output(sArgs);
	},
	
	/**
	 * @post go to the url
	 * @param Core.Url oUrl is the url path to go
	 */
	go: function(oUrl) {
		if(this.m_sMode == 'action') go(oUrl.get());
	},
	
	/**
	 * @post suspends activity for the specified number of seconds
	 * @param float fSleepTime is the time to sleep in seconds
	 */
	sleep: function(fSleepTime) {
		sleep(fSleepTime);
	},
	
	/**
	 * @post wait until oPath is found on the page
	 		 (the function abord waiting if it wait to much)
	 * @param Pattern oPath is the path to find
	 * @iIndex Int (optional)[default = 0]
	 * @return MatchPointer
	 */
	waitForPath: function(oPath, iIndex) {
		if(iIndex==undefined) {
			iIndex = 0;
		}
		
		var iTest = 10;
		var oMatchesPtr;
		while(iTest > 0)
		{
			oMatchesPtr = this.find(oPath);
			
			if(oMatchesPtr.hasMatch) {
				break;
			}
			this.sleep(this.m_fSleepTime);
			this.output("waiting to find path...");
			iTest--;
		}
		
		while(iIndex && oMatchesPtr.hasMatch)
		{
			oMatchesPtr = oMatchesPtr.next;
			iIndex--;
		}
		return oMatchesPtr;
	},
	
	/**
	 * @post find element from oPath
	 * @param Pattern oPath is the path to find
	 * @return Match pointer
	 */
	find: function(oPath) {
		return find(oPath);
	},
	
	/**
	 * @post click on element from oPath
	 * @param Pattern oPath is the path were the click will be done
	 * @iIndex Int (optional)[default = 0]
	 */
	click: function(oPath, iIndex) {
		var oMatchesPtr = this.waitForPath(oPath, iIndex);
		if(this.m_sMode == 'action') click(oMatchesPtr);
		return oMatchesPtr;
	},
	
	/**
	 * @post enter sValue on element from pPath
	 * @param Pattern oPath is the path were the click will be done
	 * @sValue String value to enter
	 * @iIndex Int (optional)[default = 0]
	 */
	enter: function(oPath, sValue, iIndex) {
		var oMatchesPtr = this.waitForPath(oPath, iIndex);
		if(this.m_sMode == 'action') enter(oMatchesPtr, sValue);
		return oMatchesPtr;
	},
	
	/**
	 * @post pick sValue on element from pPath
	 * @param Pattern oPath is the path were the click will be done
	 * @sValue String value to pick
	 * @iIndex Int (optional)[default = 0]
	 */
	pick: function(oPath, sValue, iIndex) {
		var oMatchesPtr = this.waitForPath(oPath, iIndex);
		if(this.m_sMode == 'action') pick(oMatchesPtr, sValue);
		return oMatchesPtr;
	},
	
	/**
	 * @post check element from pPath
	 * @param Pattern oPath is the path were the click will be done
	 * @sValue String value ("true" or "false") to check
	 * @iIndex Int (optional)[default = 0]
	 */
	check: function(oPath, sValue, iIndex) {
		var oMatchesPtr = this.waitForPath(oPath, iIndex);
		if(sValue == "true") {
			if(this.m_sMode == 'action') check(oMatchesPtr);
		} else if(sValue == "false") {
			if(this.m_sMode == 'action') uncheck(oMatchesPtr);
		}
		return oMatchesPtr;
	},
	
	/**
	 * @post drag from oPathSrc to oPathDest
	 * @param Pattern oPathSrc is the path were the drag will be done
	 * @param Pattern oPathDest is the path were the drop will be done
	 * @iIndex array<Int> (optional)[default = {'src':0, 'dest':0}]
	 */
	dradAndDrop: function(oPathSrc, oPathDest, iIndexes)
	{
		if(iIndexes==undefined) {
			iIndexes = {'src':0, 'dest':0};
		}
		
		//for now, the following code is "mootools - drag and drop" compatible
		//it has not been test on other drag and drop javascript library

		var oMatchesSrcPtr = this.waitForPath(oPathSrc, iIndexes['src']);
		
		//call drag on source
		this.fireMouseEvent('mousedown', oMatchesSrcPtr.element);
		
		//call drop on destination
		var oMatchesDestPtr = this.waitForPath(oPathDest, iIndexes['dest']);
		var coords = oMatchesDestPtr.element.wrappedJSObject.getCoordinates();
		var mouseCoords = {'clientX':(coords.left + coords.right)/2, 'clientY':(coords.top + coords.bottom)/2};
		//start drag
		this.fireMouseEvent('mousemove', oMatchesDestPtr.element, mouseCoords);
		//drag
		this.fireMouseEvent('mousemove', oMatchesDestPtr.element, mouseCoords);
		//drag
		this.fireMouseEvent('mousemove', oMatchesDestPtr.element, mouseCoords);
		//drop
		this.fireMouseEvent('mouseup', oMatchesDestPtr.element, mouseCoords);

		return {'src':oMatchesSrcPtr, 'dest':oMatchesDestPtr};
	},
	
	/**
	 * @post Takes a node and fires the appropriate mouse event on it via automation.
	 * @param sType is the name of the type of mouse event,
	 *        such as: "mousedown", "mouseup", or "click"
	 * @param oNode the Node that should receive the event
	 * @param oArgs add args
	 * @return boolean indicating whether any of the listeners which handled the
	 *         event called preventDefault. If preventDefault was called
	 *         the value is false, else the value is true.
	 */
	fireMouseEvent: function(sType, oNode, oArgs) {
		var oDefaultArgs = $H({
			'canBubble':true,
			'cancelable':true,
			'detail':1,
			'screenX':0,
			'screenY':0,
			'clientX':0,
			'clientY':0,
			'ctrlKey':false,
			'altKey':false,
			'shiftKey':false,
			'metaKey':false,
			'button':0,
			'relatedTarget':null
		});
		oArgs = oDefaultArgs.merge(oArgs);
		
		// Mozilla spec for initMouseEvent:
		//   http://www.mozilla.org/docs/dom/domref/dom_event_ref29.html
		// JavaScript example of using initMouseEvent:
		//   http://www.adras.com/Fire-an-event-from-javascript.t69-50.html
		
		var doc = oNode.ownerDocument;
		var event = doc.createEvent("MouseEvents");
		event.initMouseEvent(
			sType, // typeArg
			oArgs['canBubble'], // canBubbleArg
			oArgs['cancelable'], // cancelableArg
			doc.defaultView, // viewArg (type AbstractView)
			oArgs['detail'], // detailArg (click count)
			oArgs['screenX'], // screenX
			oArgs['screenY'], // screenY
			oArgs['clientX'], // clientX
			oArgs['clientY'], // clientY
			oArgs['ctrlKey'], // ctrlKeyArg
			oArgs['altKey'], // altKeyArg
			oArgs['shiftKey'], // shiftKeyArg
			oArgs['metaKey'], // metaKeyArg
			oArgs['button'], // buttonArg (0: left, 1: middle, 2: right)
			oArgs['relatedTarget'] // relatedTargetArg
		);
		  
		// http://www.xulplanet.com/references/objref/EventTarget.html#method_dispatchEvent
		//if (doc instanceof XULElement) {
		//	return oNode.click()
		//}
		//else {
			return oNode.dispatchEvent(event);
		//}
		//also execute a direct click() command to the node
		//just in case it is an xbl binding or anonymous content that doesn't
		//respond to node.dispatchEvent
	}
};