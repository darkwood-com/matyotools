var Test = Chickenfoot.Test;

var t = new Test();

t.test("click", function(){
  go("http://www.google.com");
  click("advanced search");
  Test.assert(/advanced_search/.test(document.location));
  click("Advanced Search button");
  Test.assert(/webhp/.test(document.location));
});

// regression test for bug #293
t.test("click", function() {
  go("http://www.google.com", true)
  insert(after("google search button"),
    "<a href='http://web.mit.edu' target='mit'>MIT</a>")
  click("mit")
  Test.assert(/www.google.com/.test(document.location));
  sleep(0.5)
  closeTabFrom("http://web.mit.edu/")
});

// regression tests for bug #288
t.test("click", function() {
  go("file://" + scriptDir.path + "/pages/housingbubbleblog.html");
  var m = find(new XPath("//img[@class='collapseicon']"))
  click(m)
});

// regression test for javascript: URLs
t.test("click", function() {
  go("http://www.google.com/m/classic")
  click("images")
  sleep(0.1)
  Test.assert(/images.google.com/.test(document.location));
});

t.close();


function closeTabFrom(/*String*/ url) {
  var tabBrowser = Chickenfoot.getTabBrowser(chromeWindow)
  var tabs = tabBrowser.mTabBox._tabs.childNodes;
  for (var i = 0; i < tabs.length; ++i) {
    var tb = tabs[i];
    var browser = tabBrowser.getBrowserForTab(tb);
    if (browser.contentWindow.location == url) {
      browser.contentWindow.close()
      return;
    }
  }
  //throw new Error("can't find tab from " + url + " to close")
}





