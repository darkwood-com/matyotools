define(['matyotools', 'exec'], function(matyotools) {
    matyotools.exec.childs.mount = function(argv) {
        var fs = require('fs-extra');
        var execSync = require('execSync');
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
                ssh[name].localdir = ('/Volumes/{user}@{host}').replaceAll(ssh[name]);

                if(fs.existsSync(ssh[name].localdir)) {
                    execSync.exec([
                        'if mount | grep {localdir} ; then',
                        'umount {localdir} && sleep 1s;',
                        'fi'
                    ].join("\n").replaceAll(ssh[name]));
                } else {
                    fs.mkdirsSync(ssh[name].localdir);
                }

                console.log(execSync.exec([
                    'sshfs {user}@{host}:{path} {localdir} -o volname={user}@{host}',
                    ' && echo "mounted {user}@{host}:{path} on {localdir}"',
                    ' || echo "could not mount {user}@{host}:{path} on {localdir}"'
                ].join('').replaceAll(ssh[name])));
            } else {
                program.help();
            }
        }
    };
});