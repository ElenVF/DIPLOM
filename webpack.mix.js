const mix = require('laravel-mix');
const path = require('path');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('src/app.js', 'js')
    .js('src/account.js', 'js')
    .js('src/books.js', 'js')
    .vue()
    .sass('src/css/app.scss', 'css').setPublicPath('web').sourceMaps();

mix.alias({
    vue$: path.join(__dirname, 'node_modules/vue/dist/vue.esm-bundler.js'),
    '@': path.resolve(__dirname, 'src'),
    '@c': path.resolve(__dirname, 'src/components'),
    '@i': path.resolve(__dirname, 'src/images'),
    '@css': path.resolve(__dirname, 'src/css'),
    parse: path.resolve(__dirname, '/node_modules/parse/dist/parse.min.js')
});