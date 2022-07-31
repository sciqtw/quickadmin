let mix = require('laravel-mix')

mix.webpackConfig({
    plugins: [

    ],
    externals: {
        // 'element-ui': 'Element',
        // 'axios': 'axios',
        'vue': 'Vue',
        // 'vuedraggable': 'vuedraggable',
        // 'vuex': 'Vuex',
        // 'vue-router': 'VueRouter'
    }
})


mix.setPublicPath('dist')
    .js('js/field.js', 'js').vue({ version: 3 })
    // .js('js/field.js', 'js').vue()
    // .sass('resources/sass/field.scss', 'css')
