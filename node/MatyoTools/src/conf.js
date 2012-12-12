define(['matyotools'], function(matyotools) {
    var path = require('path-extra');
    var fs = require('fs');

    var Conf = function() {
        this.name = ".matyotools";
    };

    Conf.prototype = {
        get: function(key) {
            var filepath = path.homedir() + '/' + this.name;
            if(fs.existsSync(filepath))
            {
                var conf = fs.readFileSync(filepath);
                conf = JSON.parse(conf);

                return conf[key] || null;
            }
            else
            {
                return null;
            }
        },

        put: function(key, value) {
            var conf = {};

            var filepath = path.homedir() + '/' + this.name;
            if(fs.existsSync(filepath))
            {
                conf = fs.readFileSync(filepath);
                conf = JSON.parse(conf);
            }

            conf[key] = value;

            conf = JSON.stringify(conf);
            fs.writeFileSync(filepath, conf);
        }
    };

    matyotools.conf = new Conf();
});