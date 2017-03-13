require("../../../../../themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

var schedule_api     = require('./schedule-api.js');

require('./event-list.tag');
require('./schedule-event.tag');
require('./schedule-global-filter.tag');

riot.mount('schedule-global-filter');
riot.mount('event-list',{ schedule_api: schedule_api});
