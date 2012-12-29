requirejs([
    "matyotools",
    "exec",
    "exec/mount",
    "exec/selfupdate",
    "exec/ssh",
    "exec/ssh/add",
    "exec/ssh/get",
    "exec/ssh/go",
    "exec/ssh/list",
    "exec/svn",
    "exec/svn/add"
], function(matyotools) {
    var argv = process.argv;

    matyotools.exec.call(argv);
});