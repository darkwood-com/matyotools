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
                        requireLib:"../node_modules/grunt-contrib-requirejs/node_modules/requirejs/require"
                    },
                    include:['requireLib'],
                    out:'matyotools.js'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-requirejs');
    grunt.registerTask('build', 'requirejs');
};
