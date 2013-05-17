define(['matyotools', 'exec/ssh'], function(matyotools) {
    matyotools.exec.childs.ssh.childs.go = function(argv) {
        var program = require('commander');

        /**
         * https://github.com/thomasfr/node-simple-replace
         */
        String.prototype.replaceAll = function(objectHash) {
            var placeholerDefaultValueRegex = /([^:-]+)+:-(.*)+/;
            var placeholderRegex = /(?:\{([^}]+)+\})+?/g;
            var defaultValueMatches;
            var placeholderReplace = function (placeholder, configVar) {
                if (typeof objectHash[configVar] !== 'undefined') {
                    return objectHash[configVar];
                } else if (!configVar.match(placeholerDefaultValueRegex)) {
                    return placeholder;
                } else {
                    defaultValueMatches = configVar.match(placeholerDefaultValueRegex);
                    if (typeof objectHash[defaultValueMatches[1]] !== 'undefined') {
                        return objectHash[defaultValueMatches[1]];
                    } else {
                        return defaultValueMatches[2];
                    }
                }
            };
            return this.replace(placeholderRegex, placeholderReplace);
        };

        program
            .version('0.0.1')
            .description('mount {name}')
            .option('-l, --list', 'display mount list')
            .parse(argv);

        var ssh = matyotools.conf.get('ssh') || {};

        if(program.list) {
            for(var name in ssh)
            {
                console.log(('{name} -> {user}@{host}:{path}').replaceAll(ssh[name]));
            }
        } else {
            var name = argv.splice(2,1).shift();

            if(name && ssh[name]
                && ssh[name].user
                && ssh[name].host
                && ssh[name].path) {
                console.log(('ssh {user}@{host}').replaceAll(ssh[name]));
            } else {
                program.help();
            }
        }
    };
});