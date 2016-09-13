module.exports = {
  entry: {
    "js/public/software_all_projects": "./js/software_all_projects.js",
    "js/public/software_sample_configs":"./js/software_sample_configs.js",
  },
  
  output: {
    path: __dirname ,
    filename: "[name].bundle.js"
  }
};