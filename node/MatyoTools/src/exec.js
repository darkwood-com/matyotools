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

define(['matyotools', 'exec/mount', 'exec/svn'], function(matyotools) {
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