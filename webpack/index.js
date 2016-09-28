"use strict";

const fs = require('fs');
const path = require('path');
const webpack = require('webpack');
const minimist = require('minimist');
const baseConfig = require('./base-config');
const DashboardPlugin = require('webpack-dashboard/plugin');
const outputHandler = require('./outputHandler');
const iterate = require('./objectIterator');
const createServer = require('./createServer');
const TARGET = process.env.npm_lifecycle_event;
const args = minimist(process.argv.slice(1));
const moduleName = args._[1];

if(!moduleName) {
	console.error('Please provide a module name: node webpack-serve [your-module]');
	process.exit(1);
}

const modulePath = `../${moduleName}`;

try {
	fs.statSync(path.join(__dirname, modulePath));
} catch (e) {
	console.error(`${modulePath} is not a directory`);
	process.exit(1);	
}

const configPath = path.join(__dirname, modulePath, 'webpack.config.js');

try {
	fs.existsSync(configPath)
} catch (e) {
	console.error(`There is no webpack.config.js file in ${moduleName}`);
	process.exit(1);
}

const moduleConfig = require(configPath);
const config = Object.assign({}, baseConfig, moduleConfig);

// merge plugins
if(Array.isArray(moduleConfig.plugins)) {
	config.plugins = baseConfig.plugins.concat(moduleConfig.plugins);
}

// Set the paths relative to root
let newEntries = {};	
iterate(config.entry, (bundleFile, paths) => {
	paths = !Array.isArray(paths) ? [paths] : paths;
	newEntries[bundleFile] = paths.map(p => './'+path.join(moduleName, p));
});

config.entry = newEntries;

switch(TARGET) {
	case 'serve':
	  	config.plugins.push(new DashboardPlugin());
	  	return createServer(config);

	case 'watch':
		config.plugins.push(new DashboardPlugin());
		return webpack(config).watch({}, outputHandler);
	
	default:
		webpack(config).run((err, stats) => {
			outputHandler(err, stats);
			process.exit(0);
		});
}