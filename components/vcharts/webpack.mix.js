let mix = require('laravel-mix')

mix.webpackConfig({
    plugins: [

    ],
    externals: {
        'vue': 'Vue',
    }
})


mix.setPublicPath('dist')
    .js('resources/js/field.js', 'js').vue({ version: 3 })
