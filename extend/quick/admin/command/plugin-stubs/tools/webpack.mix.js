let mix = require('laravel-mix')

mix.webpackConfig({
    plugins: [],
    externals: {
        'vue': 'Vue',
    }
})


mix.setPublicPath('dist')
    .js('js/field.js', 'js').vue({ version: 3 })
    // .js('js/field.js', 'js').vue()
    // .sass('resources/sass/field.scss', 'css')
