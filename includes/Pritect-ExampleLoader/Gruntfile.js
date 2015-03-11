module.exports = function (grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        replace: {
            namespaces: {
                src: ['includes/**/*.php'],
                overwrite: true,
                replacements: [{
                    // Replaces lines that look like: namespace Pritect\ExampleLoader\v1.0.0;
                    from: /^namespace\s*(.*)\\(.*)\\(v[\w_]+)(.*);$/m,
                    to: function (matchedWord, index, fullText, regexMatches) {
                        return 'namespace ' + regexMatches[0] + '\\' + regexMatches[1] + '\\v' + grunt.config('pkg.version').replace(/\./g, '_') + regexMatches[3] + ';';
                    }

                }]
            },
            bootstrap: {
                src: ['example-loader.php'],
                overwrite: true,
                replacements: [
                    {
                        from: /Version:\s*(.*)/,
                        to: 'Version: <%= pkg.version %>'
                    },
                    {
                        from: /class_exists\(\s*'(.*)_Bootstrap_v(.*)'\s*\)/,
                        to: function (matchedWord, index, fullText, regexMatches) {
                            return 'class_exists( \'' + regexMatches[0] + '_Bootstrap_v' + grunt.config('pkg.version').replace(/\./g, '_') + '\' )';

                        }
                    },
                    {
                        from: /class (.*)_Bootstrap_(.*)\s*\{/,
                        to: function (matchedWord, index, fullText, regexMatches) {
                            return 'class ' + regexMatches[0] + '_Bootstrap_v' + grunt.config('pkg.version').replace(/\./g, '_') + ' {';
                        }

                    }


                ]
            }
        }
    });

    grunt.loadNpmTasks('grunt-text-replace');

    grunt.registerTask('update_version', ['replace:namespaces', 'replace:bootstrap']);
};
