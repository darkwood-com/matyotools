define(['matyotools', 'exec'], function(matyotools) {
    matyotools.exec.childs.selfupdate = function(argv) {
        var https = require('https');
        var fs = require('fs');

        var url = "https://raw.github.com/matyo91/MatyoTools/master/node/MatyoTools/matyotools.js";
        var file = argv[1];

        https.get(url, function(res) {
            res.on('data', function(data) {
                fs.writeFile(file, data);
            });
        })
    };
});