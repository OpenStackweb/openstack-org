var webpack = require('webpack');

module.exports = {
  entry: './app/index',
  output: {
    path: __dirname + '/public/',
    filename: 'bundle.js'
  },
  plugins: [
    new webpack.ProvidePlugin({
      riot: 'riot'
    })
  ],
  module: {
    loaders: [
      { test: /\.tag$/, loader: 'tag' }
    ]
  },
  devServer: {
    contentBase: './app'
  },
  devServer: {
    contentBase: './public'
  }
};