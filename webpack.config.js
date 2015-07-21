var ExtractTextPlugin = require("extract-text-webpack-plugin");
var webpack = require('webpack');

module.exports = {  
  entry: './summit/javascript/src/index.js',
  output: {
    path: './summit/javascript/build', // This is where images AND js will go
    publicPath: '', // This is used to generate URLs to e.g. images
    filename: 'bundle.js'
  },

  module: {
    loaders: [,
      { 
        test: /\.js$/, 
        loader: 'babel-loader' 
      },
      { 
        test: /\.less$/, 
        loader: ExtractTextPlugin.extract("style-loader", "css-loader!less-loader")
      },
      { 
        test: /\.css$/, 
        loader: ExtractTextPlugin.extract("style-loader", "css-loader")
      },
      { 
        test: /\.(otf|eot|svg|ttf|woff)$/,
        loader: 'url-loader?limit=8192' 
      }
    ]
  },
  resolve: {
      modulesDirectories: ["node_modules", "bower_components"]
  },

  plugins: [
    new ExtractTextPlugin("[name].css"),
    new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"]),
    new webpack.optimize.UglifyJsPlugin({minimize: true})         
  ]  
};