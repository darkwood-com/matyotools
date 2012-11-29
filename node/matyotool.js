#! /usr/bin/env node

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

var fs = require('fs-extra');
var execSync = require('exec-sync');

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

var VERSION = '1.0';

var conf = {
	'user'		:'by',
	'host'		:'lamp',
	'hostdir'	:'/var/www',
	'localdir'	:'/Volumes/lamp',
};

if(true) {
	conf = {
		'user'		: 'root',
		'host'		: 'lamp',
		'hostdir'	: '/',
		'localdir'	: '/Volumes/root_lamp',
	};
}

if(fs.existsSync(conf['localdir'])) {
	execSync([
		'if mount | grep {localdir} ; then',
			'umount {localdir} && sleep 1s;',
		'fi',
	].join("\n").replaceAll(conf));
} else {
	fs.mkdirsSync(conf['localdir']);
}

execSync([
	'sshfs {user}@{host}:{hostdir} {localdir} -o volname={user}@{host}',
	' && echo "mounted {user}@{host}:{hostdir} on {localdir}"',
	' || echo "could not mount {user}@{host}:{hostdir} on {localdir}"',
].join('').replaceAll(conf));
