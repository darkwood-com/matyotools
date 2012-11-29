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

String.prototype.replaceAll = function() {
	return arguments[0];
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
	console.log([
		'if mount | grep $1 ; then',
			'umount $1 && sleep 1s;',
		'fi',
	].join("\n").replaceAll(conf['localdir']));
} else {
	fs.mkdirsSync(conf['localdir']);
}

/*
execSync([
	'sshfs $user@$host:$hostdir $localdir -o volname=$user@$host',
	' && echo "mounted $user@$host:$hostdir on $localdir"',
	' || echo "could not mount $user@$host:$hostdir on $localdir"',
].join());
*/
