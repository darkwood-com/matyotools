#!/usr/bin/env node

/**
 * Copyright (C) Mathieu Ledru [http://www.darkwood.fr]
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

(function(define) {
/**
 * Copyright (C) Mathieu Ledru [http://www.darkwood.fr]
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('matyotools',{ VERSION: '1.0' });
define('exec',['matyotools'], function(matyotools) {
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
                case 'svn':
                    matyotools.exec.childs.svn.call(argv);
                    break;
            }
        }
    };
});
define('exec/mount',['matyotools', 'exec'], function(matyotools) {
    matyotools.exec.childs.mount = function(argv) {
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
            .description('mount by@lamp')
            .option('-r, --root', 'mount root@lamp')
            .parse(argv);

        var conf = {
            'user'		:'by',
            'host'		:'lamp',
            'hostdir'	:'/var/www',
            'localdir'	:'/Volumes/lamp'
        };

        if(program.root) {
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
define('exec/selfupdate',['matyotools', 'exec'], function(matyotools) {
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
define('exec/svn',['matyotools', 'exec'], function(matyotools) {
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
define('exec/svn/add',['matyotools', 'exec/svn'], function(matyotools) {
    matyotools.exec.childs.svn.childs.add = function(argv) {
        var fs = require('fs-extra');

        if(fs.existsSync('.svn')) {
            var program = require('commander');
            var execSync = require('exec-sync');

            program
                .version('0.0.1')
                .option('-u, --unversioned', 'add unversionned files')
                .parse(argv);

            if(program.unversioned) {
                console.log(execSync('svn st | grep ^\\? | awk {\'print "svn add "\\$2\'} | sh'));
            }
        } else {
            console.log("svn: warning: '.' is not a working copy");
        }
    };
});
/**
 * Copyright (C) Mathieu Ledru [http://www.darkwood.fr]
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

var requirejs = require('requirejs');

requirejs.config({
    nodeRequire: require
});

requirejs([
    "matyotools",
    "exec",
    "exec/mount",
    "exec/selfupdate",
    "exec/svn",
    "exec/svn/add"
], function(matyotools) {
    var argv = process.argv;

    matyotools.exec.call(argv);
});

define("main", function(){});
}(require('requirejs').define));