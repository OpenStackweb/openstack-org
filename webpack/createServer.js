const webpack = require('webpack');
const WebpackDevServer = require('webpack-dev-server');
const iterate = require('./objectIterator');

const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || 'localhost';
const CLIENT_SERVER = 'http://' + HOST + ':' + PORT;

module.exports = (config) => {	
	const newEntries = {};

	iterate(config.entry, (bundleFile, paths) => {
		newEntries[bundleFile] = [
			'webpack-dev-server/client?' + CLIENT_SERVER +'/',
			'webpack/hot/only-dev-server'
		].concat(paths)
	});

	config.entry = newEntries;
  	config.output.publicPath = 'http://127.0.0.1:3000/production';
  	config.plugins.push(
  		new webpack.HotModuleReplacementPlugin()
  	);

	return new WebpackDevServer(webpack(config), {
	  publicPath: config.output.publicPath,
	  quiet: true,	  
	  hot: true,
	  moduleBind: 'css=style!css',
	  historyApiFallback: true,
	  stats: {
	    colors: true
	  }
	}).listen(3000, 'localhost', err => {
	    if (err) {
	      console.log(err);
	    }

	    console.log('Listening at localhost:3000');
	});
};