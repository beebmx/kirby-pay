let mix = require('laravel-mix');

mix
  .js('js/app.js', 'dist/')
  .sass('css/app.scss', 'dist/')
  .sass('css/oxxo.scss', 'dist/')
  .copy('dist', '../assets')
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve(__dirname, 'js/'),
      },
    },
  })
  .disableSuccessNotifications()
