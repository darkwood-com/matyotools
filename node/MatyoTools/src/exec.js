define(['matyotools', 'conf'], function(matyotools) {
    matyotools.exec = {
        childs: {},
        call: function(argv) {
            var prog = argv.splice(2,1).shift();

            switch(prog) {
                case 'mount':
                    matyotools.exec.childs.mount(argv);
                    break;
                case 'selfupdate':
                    matyotools.exec.childs.selfupdate(argv);
                    break;
                case 'ssh':
                    matyotools.exec.childs.ssh.call(argv);
                    break;
                case 'svn':
                    matyotools.exec.childs.svn.call(argv);
                    break;
            }
        }
    };
});