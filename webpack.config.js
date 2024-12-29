const Encore = require('@symfony/webpack-encore');
const path = require('path');
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // Public path used by the web server to access the output path
    .setPublicPath('/build')
    // Only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // Entry point for your app
    .addEntry('app', './assets/app.js')
    .addEntry('admin_calendar', './assets/admin/calendar.js')
    .addEntry('admin', './assets/admin/admin.js')
    .addEntry('student_calendar', './assets/student/calendar.js')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // Will require an extra script tag for runtime.js (default: true)
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // Enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // Enable SCSS
    .enableSassLoader()

    // Process JS files
    .configureBabel(() => { }, {
        useBuiltIns: 'usage',
        corejs: 3
    })

    // Enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // Enable PostCSS loader
    .enablePostCssLoader()
    ;

module.exports = Encore.getWebpackConfig();
