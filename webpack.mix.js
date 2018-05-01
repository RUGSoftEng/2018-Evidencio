let mix = require('laravel-mix');

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
if (!mix.inProduction()) {
  mix.webpackConfig({
      devtool: 'source-map'
  })
  .sourceMaps()
}

mix.js('resources/assets/js/app.js', 'public/js')
   .js('resources/assets/js/workflow.js', 'public/js')
   .js('resources/assets/js/sidebar.js', 'public/js')
   .js('resources/assets/js/designer.js', 'public/js')
   .js('resources/assets/js/bootstrap-colorpalette.js', 'public/js')
   .js('resources/assets/js/ya-simple-scrollbar.js', 'public/js')
   .sass('resources/assets/sass/app.scss', 'public/css')
   .sass('resources/assets/sass/workflow.scss', 'public/css')
   .sass('resources/assets/sass/sidebar.scss', 'public/css')
   .sass('resources/assets/sass/designer.scss', 'public/css');
