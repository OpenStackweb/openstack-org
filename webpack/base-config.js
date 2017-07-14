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
    new ExtractTextPlugin({ filename: 'css/[name].css' }),
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
function styleLoader(loaders) {
    if (PRODUCTION)
        return ExtractTextPlugin.extract({ fallback: 'style-loader', use: loaders });
    return [ 'style-loader', ...loaders ];
}

/**
 *
 * @returns {object}
 */
function postCSSLoader() {
    return {
        loader: "postcss-loader",
        options: {
            plugins: function () {
                return [require("autoprefixer")];
            }
        }
    }
}

/**
 *
 * @type {*[]}
 */
var rules = [
    {
        test: /\.js$/,
        use: [
            'react-hot-loader', 'babel-loader'
        ],
        exclude: EXCLUDE_JS,
        include: path.join(__dirname, '../')
    }, {
        test: /\.tag$/,
        use: [
            'babel-loader', 'tag-loader'
        ]
    }, {
        test: /\.json$/,
        use: ['json-loader']
    }, {
        test: /\.module\.css$/,
        use: styleLoader(['css-loader?modules', postCSSLoader()])
    }, {
        test: /\.module\.less/,
        use: styleLoader(['css-loader?modules', postCSSLoader(), 'less-loader'])
    }, {
        test: /\.module\.scss/,
        use: styleLoader(['css-loader?modules', postCSSLoader(), 'sass-loader'])
    }, {
        test: /\.css$/,
        exclude: /\.module\.css$/,
        use: styleLoader(['css-loader', postCSSLoader()])
    }, {
        test: /\.less/,
        exclude: /\.module\.less/,
        use: styleLoader(['css-loader', postCSSLoader(), 'less-loader'])
    }, {
        test: /\.scss/,
        exclude: /\.module\.scss/,
        use: styleLoader(['css-loader', postCSSLoader(), 'sass-loader'])
    }, {
        test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        use: "url-loader?limit=10000&minetype=application/font-woff&name=fonts/[name].[ext]"
    }, {
        test: /\.(ttf|eot)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        use: "file-loader?name=fonts/[name].[ext]"
    }, {
        test: /\.jpg|\.png|\.gif$/,
        use: "file-loader?name=images/[name].[ext]"
    }, {
        test: /\.svg/,
        use: "file-loader?name=svg/[name].[ext]!svgo-loader"
    }
];

module.exports = {

    module: {
        rules
    },
    plugins: PRODUCTION
        ? plugins.concat(productionPlugins)
        : plugins.concat(devPlugins),
    resolve: {
        alias: {
            '~core': path.join(__dirname, '../ui-core/ui/source'),
            '~core-components': path.join(__dirname, '../ui-core/ui/source/js/components'),
            '~core-css': path.join(__dirname, '../ui-core/ui/source/css'),
            '~core-utils': path.join(__dirname, '../ui-core/ui/source/js/utils')
        }
    }
};
