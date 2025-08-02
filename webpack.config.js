const defaultConfig = require('@wordpress/scripts/config/webpack.config');

module.exports = {
    ...defaultConfig,
    entry: {
        'settings': ['./src/admin/settings.js', './src/admin/settings.css'],
    },
    output: {
        path: __dirname + '/assets/admin',
        filename: '[name].js',
    },
}; 