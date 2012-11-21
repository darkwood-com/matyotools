
var testsRun = [];
debug = Chickenfoot.debug;

runTest("findTest.js");
runTest("clickTest.js");
runTest("pickTest.js");
runTest("checkTest.js");
runTest("xpathTest.js");
runTest("goTest.js");
runTest("SetTest.js");
runTest("namespaceTest.js");
runTest("waitTest.js");
runTest("instanceofTest.js");
runTest("recorderTest.js");
runTest("TextBlobTest.js");
runTest("MatchTest.js");
runTest("stringsTest.js");
runTest("includeTest.js");
runTest("crashTest.js");
runTest("insertTest.js");
runTest("removeTest.js");
runTest("replaceTest.js");
runTest("resetTest.js");
runTest("keypressTest.js");
//runTest("xulmatchingTest.js"); not ready yet


debug("*********** SUMMARY *************** ")
for (var i = 0; i < testsRun.length; ++i) {
  var test = testsRun[i];
  debug(test.name + " " + test);
}

function runTest(name) {
  debug(name + " ****************************** ")
  
  var test = include(name, {});
  if (test) {
    test.name = name;
    testsRun.push(test);
  }
}
