var webpack = require('webpack');
var ExtractTextPlugin = require('extract-text-webpack-plugin');
var path = require('path');
var PRODUCTION = process.env.NODE_ENV === 'production';

var EXCLUDE_JS = [/node_modules/];

/**
 *
 * @type {*[]}
 */
var plugins = [
    new ExtractTextPlugin('css/[name].css'),
    new webpack.ProvidePlugin({riot: 'riot'})
];

/**
 * Plugins that will only be included in development
 * @type {Array}
 */
var devPlugins = [];

/**
 * Plugins that will only be included in production
 * @type {*[]}
 */
var productionPlugins = [
    new webpack.optimize.UglifyJsPlugin({minimize: true}),
    new webpack.DefinePlugin({
        'process.env': {
            'NODE_ENV': JSON.stringify('production')
        }
    })
];

/**
 * Styles should only be extracted in production to allow hot-reload of css
 * @param loader
 * @returns {*}
 */
function styleLoader(loader) {
    if (PRODUCTION)
        return ExtractTextPlugin.extract('style-loader', loader);
    return 'style-loader!' + loader;
}

/**
 *
 * @returns {string}
 */
function autoPrefixLoader() {
    return 'autoprefixer-loader?' + JSON.stringify({
        browsers: ['Firefox > 20', 'iOS 7', 'IE 9']
    });
}

/**
 *
 * @type {*[]}
 */
var loaders = [
    {
        test: /\.js$/,
        loaders: [
            'react-hot', 'babel'
        ],
        exclude: EXCLUDE_JS,
        include: path.join(__dirname, '../')
    }, {
        test: /\.tag$/,
        loader: 'babel!tag'
    }, {
        test: /\.json$/,
        loaders: ['json-loader']
    }, {
        test: /\.module\.css$/,
        loader: styleLoader('css-loader?modules!' + autoPrefixLoader())
    }, {
        test: /\.module\.less/,
        loader: styleLoader('css-loader?modules!' + autoPrefixLoader() + '!less-loader')
    }, {
        test: /\.module\.scss/,
        loader: styleLoader('css-loader?modules!' + autoPrefixLoader() + '!sass-loader')
    }, {
        test: /\.css$/,
        exclude: /\.module\.css$/,
        loader: styleLoader('css-loader!' + autoPrefixLoader())
    }, {
        test: /\.less/,
        exclude: /\.module\.less/,
        loader: styleLoader('css-loader!' + autoPrefixLoader() + '!less-loader')
    }, {
        test: /\.scss/,
        exclude: /\.module\.scss/,
        loader: styleLoader('css-loader!' + autoPrefixLoader() + '!sass-loader')
    }, {
        test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        loader: "url-loader?limit=10000&minetype=application/font-woff&name=fonts/[name].[ext]"
    }, {
        test: /\.(ttf|eot)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        loader: "file-loader?name=fonts/[name].[ext]"
    }, {
        test: /\.jpg|\.png|\.gif$/,
        loader: "file-loader?name=images/[name].[ext]"
    }, {
        test: /\.svg/,
        loader: "file-loader?name=svg/[name].[ext]!svgo-loader"
    }
];

module.exports = {

    module: {
        loaders
    },
    plugins: PRODUCTION
        ? plugins.concat(productionPlugins)
        : plugins.concat(devPlugins),
    resolve: {
        alias: {
            '~core': path.join(__dirname, '../ui-core/ui/source'),
            '~core-components': path.join(__dirname, '../ui-core/ui/source/js/components'),
            '~core-css': path.join(__dirname, '../ui-core/ui/source/css')
        }
    }
};
