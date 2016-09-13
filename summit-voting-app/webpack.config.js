const path = require('path');

module.exports = {
  
  entry: {
    main: './source'
  },

  output: {
    filename: 'js/main.js',
    path: path.join(__dirname, 'production'),
    publicPath: path.join('/', path.basename(__dirname),'production/')
  },

};
