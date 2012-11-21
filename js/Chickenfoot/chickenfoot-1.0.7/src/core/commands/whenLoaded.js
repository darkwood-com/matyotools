/**
 * calls the given function when the given tab has finished loading
 */
function whenLoadedImpl(/*ChromeWindow*/ chromeWindow,
                         /*function*/ func, 
                         /*optional Tab or Window*/ tabOrWindow) {
  var window = undefined;
  if (!tabOrWindow) {
    window = getVisibleHtmlWindow(chromeWindow);
  } else if (instanceOf(tabOrWindow, Tab)) {
    window = tabOrWindow._window
  } else {
    window = tabOrWindow;
  }    

	var browser = getTabBrowser(chromeWindow);
	
	var wrapperFuncRan = false
	var wrapperFunc = function(event) {
		var eventWindow = event.originalTarget.defaultView;
		if (!wrapperFuncRan && eventWindow == window) {
			wrapperFuncRan = true
			browser.removeEventListener("load", wrapperFunc, true)
			// defer the actual function execution, because otherwise
			// it won't be able to do go()'s itself
			chromeWindow.setTimeout(func, 0);
		}
	}
	browser.addEventListener("load", wrapperFunc, true)
	
	var alreadyLoaded = false;
	try {
		alreadyLoaded = isWindowLoaded(window);
	} catch (e) {
		// will fall here, e.g., if it's an iframe and we don't know if
		// it's loaded
	}
	if (alreadyLoaded) {
		wrapperFunc({originalTarget : browser.contentDocument})
	}
}
