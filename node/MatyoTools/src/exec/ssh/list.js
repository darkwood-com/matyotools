define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.list = function(argv) {
        var ssh = matyotools.conf.get('ssh') || {};

        for(var host in ssh)
        {
            console.log(host);
        }
    };
});