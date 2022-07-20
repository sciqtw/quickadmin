let mix = require('laravel-mix')
mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        }
    },
})
mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js')

// mix.copy('dist/js', './../../public/static/test/js')