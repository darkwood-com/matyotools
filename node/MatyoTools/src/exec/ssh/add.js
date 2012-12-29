define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.add = function(argv) {
        var util = require('util');
        var program = require('commander');

        console.log(util.format("{name} -> ssh {user}@{host}:{path} -p {password}"));
        program.prompt({
            name: 'Name: ',
            user: 'User: ',
            host: 'Host: ',
            pass: 'Password: ',
            path: 'Path: (/) '
        }, function(opts){
            var ssh = matyotools.conf.get('ssh') || {};

            ssh[opts['name']] = {
                user: opts['user'],
                host: opts['host'],
                pass: opts['pass'],
                path: opts['path']
            };

            matyotools.conf.put('ssh', ssh);

            process.stdin.destroy();
        });
    };
});