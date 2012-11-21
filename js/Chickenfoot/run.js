/**
 * Import class
 *
 * dependency tree
 *
 * lib  -> core
 * core -> delia
 * core -> timesheet
 * core -> form
 *
 * @author Mathieu LEDRU <ml@les-argonautes.com>
 */

//default Chickenfoot library
//include("prototype.js");

//libs
include("src/lib/date.js");

//core
include("src/core/package.js");
include("src/core/xpath.js");
include("src/core/url.js");
include("src/core/cookie.js");
include("src/core/chickenfoot.js");

//delia
include("src/delia/package.js");
include("src/delia/delia.js");
include("src/delia/deliacrm.js");

//timesheet
include("src/timesheet/package.js");
include("src/timesheet/timesheet.js");

//form
include("src/form/package.js");
include("src/form/form.js");
