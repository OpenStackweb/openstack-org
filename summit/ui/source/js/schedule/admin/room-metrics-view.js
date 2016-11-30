var dispatcher = require('./reports-admin-view-dispatcher.js');

require('./room-metrics-chart.tag');
require('./room-metrics-container.tag');

riot.mount('room-metrics-container',{ dispatcher: dispatcher });