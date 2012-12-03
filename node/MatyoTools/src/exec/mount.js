define(['matyotools'], function(matyotools) {
    matyotools.mount = function(argv) {
        var fs = require('fs-extra');
        var execSync = require('exec-sync');
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
            .option('-p, --peppers', 'Add peppers')
            .parse(argv);

        var conf = {
            'user'		:'by',
            'host'		:'lamp',
            'hostdir'	:'/var/www',
            'localdir'	:'/Volumes/lamp'
        };

        if(1 == 1) {
            conf = {
                'user'		:'root',
                'host'		:'lamp',
                'hostdir'	:'/',
                'localdir'	:'/Volumes/root_lamp'
            };
        }

        if(fs.existsSync(conf['localdir'])) {
            execSync([
                'if mount | grep {localdir} ; then',
                'umount {localdir} && sleep 1s;',
                'fi'
            ].join("\n").replaceAll(conf));
        } else {
            fs.mkdirsSync(conf['localdir']);
        }

        console.log(execSync([
            'sshfs {user}@{host}:{hostdir} {localdir} -o volname={user}@{host}',
            ' && echo "mounted {user}@{host}:{hostdir} on {localdir}"',
            ' || echo "could not mount {user}@{host}:{hostdir} on {localdir}"'
        ].join('').replaceAll(conf)));
    };
});