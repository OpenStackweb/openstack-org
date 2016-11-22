"use strict";

const path = require('path');
const exec = require('child_process').exec;
const execSync = require('child_process').execSync;
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
			const packagePath = path.join(dirPath, 'ui', 'package.json');
			try {
				if(fs.statSync(configPath).isFile()) {
					const moduleName = path.basename(dirPath);
					console.log(`

/***************** 
 * Building ${moduleName}
\\*****************

`);					if(fs.existsSync(packagePath)) {
						console.log('Loading dependencies...');
						execSync(`cd ${moduleName}/ui && npm install`, {stdio: 'inherit'});
						execSync(`cd ${dir}`);
					}	
					execSync(`node ./webpack ${moduleName}`, {stdio: 'inherit'});
				}
			} catch (e) { }
		}
	});
});
