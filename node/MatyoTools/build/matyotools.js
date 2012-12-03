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

define("matyotools",{VERSION:"1.0"}),define("exec/mount",["matyotools"],function(e){e.mount=function(e){var t=require("fs-extra"),n=require("exec-sync"),r=require("commander");String.prototype.replaceAll=function(e){var t=/([^:-]+)+:-(.*)+/,n=/(?:\{([^}]+)+\})+?/g,r,i=function(n,i){return typeof e[i]!="undefined"?e[i]:i.match(t)?(r=i.match(t),typeof e[r[1]]!="undefined"?e[r[1]]:r[2]):n};return this.replace(n,i)},r.version("0.0.1").option("-p, --peppers","Add peppers").parse(e);var i={user:"by",host:"lamp",hostdir:"/var/www",localdir:"/Volumes/lamp"};i={user:"root",host:"lamp",hostdir:"/",localdir:"/Volumes/root_lamp"},t.existsSync(i.localdir)?n(["if mount | grep {localdir} ; then","umount {localdir} && sleep 1s;","fi"].join("\n").replaceAll(i)):t.mkdirsSync(i.localdir),console.log(n(["sshfs {user}@{host}:{hostdir} {localdir} -o volname={user}@{host}",' && echo "mounted {user}@{host}:{hostdir} on {localdir}"',' || echo "could not mount {user}@{host}:{hostdir} on {localdir}"'].join("").replaceAll(i)))}}),define("exec/svn",["matyotools"],function(e){e.svn=function(e){}}),define("exec",["matyotools","exec/mount","exec/svn"],function(e){e.exec=function(t){var n=t.splice(2,1).shift();switch(n){case"mount":e.mount(t);break;case"svn":e.svn(t)}}});var requirejs=require("requirejs");requirejs.config({paths:["matyotools.js","exec.js"],nodeRequire:require}),requirejs(["matyotools","exec"],function(e){var t=process.argv;e.exec(t)}),define("matyotools_dev",function(){});