define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.get = function(argv) {
        var util = require('util');
        
        var ssh = matyotools.conf.get('ssh') || {};

        var name = argv.splice(2,1).shift();
        if(ssh[name]) {
            name = ssh[name];
            console.log(util.format('ssh %s@%s:%s', name['user'], name['host'], name['pass']))
        } else {
            console.log(util.format('name %s not found', name));
        }
    };
});