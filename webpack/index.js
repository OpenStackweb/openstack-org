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
const moduleUIPath = path.join(modulePath, 'ui');
const absModulePath = path.join(__dirname, modulePath);
const absModuleUIPath = path.join(__dirname, moduleUIPath);
const absConfigPath = path.join(absModuleUIPath, 'webpack.config.js');

try {
	fs.statSync(absModulePath);
} catch (e) {
	console.error(`${modulePath} is not a directory`);
	process.exit(1);	
}

try {
	fs.statSync(absModuleUIPath);
} catch (e) {
	console.error(`${moduleName} has no ui/ directory`);
	process.exit(1);	
}

try {
	fs.existsSync(absConfigPath)
} catch (e) {
	console.error(`There is no ui/webpack.config.js file in ${moduleName}`);
	process.exit(1);
}

const moduleConfig = require(absConfigPath);
const config = Object.assign({}, baseConfig, moduleConfig);

// Set the paths relative to root
let newEntries = {};	
iterate(config.entry, (bundleFile, paths) => {
	paths = !Array.isArray(paths) ? [paths] : paths;
	newEntries[bundleFile] = paths.map(p => './'+path.join(moduleName, 'ui', p));
});

config.entry = newEntries;
config.output = {
	filename: 'js/[name].js',
	path: path.join(absModuleUIPath, 'production/'),
	publicPath: path.join('/', moduleName, 'ui/production/')
};


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