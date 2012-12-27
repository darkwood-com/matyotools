define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.list = function(argv) {
        var ssh = matyotools.conf.get('ssh') || {};

        for(var name in ssh)
        {
            console.log(name);
        }
    };
});