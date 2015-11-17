var webpack = require('webpack');

module.exports = {
  entry: {
     /* here u must define all your entries points*/
    "software/js/public/software_all_projects": "./software/js/software_all_projects",
    "software/js/public/software_sample_configs":"./software/js/software_sample_configs",
    "summit/javascript/new-trackchairs-app/app/index": "./summit/javascript/new-trackchairs-app/app/index",
    "summit/javascript/summit-highlights":"./summit/javascript/summit-highlights",
    "summit/javascript/schedule/schedule":"./summit/javascript/schedule/schedule",
    "summit/javascript/schedule/event-detail":"./summit/javascript/schedule/event-detail",
      "summit/javascript/schedule/share-buttons":"./summit/javascript/schedule/share-buttons",
  },
  output: {
    path: __dirname ,
    filename: "[name].bundle.js",
    chunkFilename: "[id].bundle.js"
  },
  plugins: [
    new webpack.ProvidePlugin({
      riot: 'riot'
    }),
    /*new webpack.optimize.UglifyJsPlugin(
          {
              sourceMap: false,
              mangle:    false,
              minimize:  true
          }
    )*/
  ],
  module: {
    loaders: [
      { test: /\.tag$/, loader: 'tag' },
    ]
  }
};