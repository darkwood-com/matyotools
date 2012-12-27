requirejs([
    "matyotools",
    "exec",
    "exec/mount",
    "exec/selfupdate",
    "exec/ssh",
    "exec/ssh/add",
    "exec/ssh/list",
    "exec/ssh/get",
    "exec/svn",
    "exec/svn/add"
], function(matyotools) {
    var argv = process.argv;

    matyotools.exec.call(argv);
});