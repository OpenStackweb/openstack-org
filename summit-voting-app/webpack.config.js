var webpack = require('webpack');
var PROD = JSON.parse(process.env.PROD || "0");

var config = {  
  entry: './javascript/index.js',
  output: {
    path: './javascript/build', // This is where images AND js will go
    publicPath: '', // This is used to generate URLs to e.g. images
    filename: PROD ? 'app.min.js' : 'app.js'
  },

  module: {
    loaders: [
      { 
        test: /\.js$/, 
        exclude: /node_modules/,
        loader: 'babel-loader' 
      }
    ]
  },
  watchOptions: {
  	poll: 300
  },
  resolve: {
      modulesDirectories: ["node_modules", "bower_components"]
  },

  plugins: [
    new webpack.ResolverPlugin.DirectoryDescriptionFilePlugin("bower.json", ["main"])   
  ]  
};

if(PROD) {
	config.plugins.push(new webpack.optimize.UglifyJsPlugin({minimize: true}));
}

module.exports = config;