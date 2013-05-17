/*global module:false*/
module.exports = function (grunt) {

    grunt.initConfig({
        requirejs:{
            matyotools:{
                options:{
                    baseUrl: "src",
                    optimize:'none',
                    name: "main",
                    paths:{
                        requireLib:"../node_modules/requirejs/require"
                    },
                    include:['requireLib'],
                    out:'matyotools.js'
                }
            },
            matyotools_bin:{
                options:{
                    baseUrl: "src",
                    optimize:'none',
                    name: "main",
                    paths:{
                        requireLib:"../node_modules/requirejs/require"
                    },
                    include:['requireLib'],
                    out:'bin/matyotools',
                    wrap: {
                        start: "#!/usr/bin/env node",
                        end: ""
                    }
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.registerTask('build', 'requirejs');
};
