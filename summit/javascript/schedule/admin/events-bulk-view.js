var dispatcher = require('./events-bulk-view-dispatcher.js');

require('./events-bulk-presentation-list.tag');
require('./events-bulk-container.tag');

riot.mount('events-bulk-container',{ dispatcher: dispatcher });