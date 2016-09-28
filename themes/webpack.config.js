var path = require('path');
var webpack = require('webpack');

module.exports = {
  entry: {
    "AwesomeButton": "./openstack/ui/source/AwesomeButton"
  },
  
  output: {
    filename: 'js/[name].bundle.js',
    path: path.join(__dirname, 'openstack/ui/production'),
    publicPath: path.join('/', path.basename(__dirname),'openstack/ui/production/')
  },

  plugins: [
    new webpack.optimize.CommonsChunkPlugin('commons.chunk.js')
  ]
};