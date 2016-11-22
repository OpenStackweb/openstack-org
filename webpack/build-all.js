"use strict";

const path = require('path');
const exec = require('child_process').exec;
const fs = require('fs');

const dir = path.join(__dirname, '../');
fs.readdir(dir, (err, list) => {
	if(err) {
		console.error(err);
		process.exit(1);
	}

	list.forEach(entry => {
		const dirPath = path.join(dir, entry);
		if(fs.statSync(dirPath).isDirectory()) {
			const configPath = path.join(dirPath, 'ui', 'webpack.config.js');
			try {
				if(fs.statSync(configPath).isFile()) {
					const moduleName = path.basename(dirPath);
					console.log(`Building ${moduleName}`);
					exec(`node ./webpack ${moduleName}`, (error, stdout, stderr) => {
					  if (error) {
					    console.error(`Error: ${error}`);
					    return;
					  }
					  console.log(stdout);
					  console.error(stderr);
					});
					
				}
			} catch (e) { }
		}
	});
});
