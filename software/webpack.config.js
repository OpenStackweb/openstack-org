var webpack = require('webpack');

module.exports = {
  entry: {
    all_projects: "./js/software_all_projects",
  },
  output: {
    path: __dirname + '/js/public/',
    filename: "[name].bundle.js",
    chunkFilename: "[id].bundle.js"
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
  resolveLoader: {
    root: [
      '/usr/lib/node_modules',
      '/usr/local/lib/node_modules'
    ]
  },
};