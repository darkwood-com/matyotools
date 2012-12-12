define(['matyotools', 'exec'], function(matyotools) {
    matyotools.exec.childs.ssh = {
        childs: {},
        call: function(argv) {
            var prog = argv.splice(2,1).shift();

            switch(prog) {
                case 'add':
                    matyotools.exec.childs.ssh.childs.add(argv);
                    break;
                case 'list':
                    matyotools.exec.childs.ssh.childs.list(argv);
                    break;
            }
        }
    };
});