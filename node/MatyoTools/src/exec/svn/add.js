define(['matyotools', 'exec/svn'], function(matyotools) {
    matyotools.exec.childs.svn.childs.add = function(argv) {
        var fs = require('fs-extra');

        if(fs.existsSync('.svn')) {
            var program = require('commander');
            var execSync = require('execSync').exec;

            program
                .version('0.0.1')
                .option('-u, --unversioned', 'add unversionned files')
                .parse(argv);

            if(program.unversioned) {
                console.log(execSync.exec('svn st | grep ^\\? | awk {\'print "svn add "\\$2\'} | sh'));
            }
        } else {
            console.log("svn: warning: '.' is not a working copy");
        }
    };
});