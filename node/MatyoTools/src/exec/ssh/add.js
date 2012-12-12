define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.add = function(argv) {
        var program = require('commander');

        program.prompt({
            name: 'Name: ',
            host: 'Host: ',
            user: 'User: ',
            pass: 'Password: '
        }, function(opts){
            var ssh = matyotools.conf.get('ssh') || {};

            ssh[opts['name']] = {
                host: opts['host'],
                user: opts['user'],
                pass: opts['pass']
            };

            matyotools.conf.put('ssh', ssh);

            process.stdin.destroy();
        });
    };
});