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
define('exec/mount',['matyotools'], function(matyotools) {
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
define('exec/svn',['matyotools'], function(matyotools) {
    matyotools.svn = function(argv) {

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

define('exec',['matyotools', 'exec/mount', 'exec/svn'], function(matyotools) {
    matyotools.exec = function(argv) {
        var prog = argv.splice(2,1).shift();

        switch(prog) {
            case 'mount':
                matyotools.mount(argv);
                break;
            case 'svn':
                matyotools.svn(argv);
                break;
        }
    }
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
    paths: [
        "matyotools.js",
        "exec.js"
    ],

    nodeRequire: require
});

requirejs(['matyotools', 'exec'], function(matyotools) {
    var argv = process.argv;

    matyotools.exec(argv);
});

define("matyotools_dev", function(){});
}(require('requirejs').define));