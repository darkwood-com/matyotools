define(['matyotools', 'exec'], function(matyotools) {
    matyotools.exec.childs.svn = {
        childs: {},
        call: function(argv) {
            var prog = argv.splice(2,1).shift();

            switch(prog) {
                case 'add':
                    matyotools.exec.childs.svn.childs.add(argv);
                    break;
            }
        }
    };
});