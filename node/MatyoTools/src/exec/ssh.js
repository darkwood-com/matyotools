define(['matyotools', 'exec'], function(matyotools) {
    var _ = require('underscore');

    matyotools.exec.childs.ssh = {
        childs: {},
        call: function(argv) {
            var prog = argv.splice(2,1).shift();

            var children = ['add','list','get','go'];
            if(_.contains(children, prog)) {
                matyotools.exec.childs.ssh.childs[prog](argv);
            } else {
                argv.push(prog);
                matyotools.exec.childs.ssh.childs.go(argv);
            }
        }
    };
});