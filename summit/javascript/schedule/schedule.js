require("./../../../themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

var schedule_api     = require('./schedule-api.js');
var schedule_filters = require('./schedule-filters.js');

require('./schedule-grid-nav.tag');
require('./schedule-global-filter.tag')
require('./schedule-main-filters.tag');
require('./schedule-event.tag');
require('./schedule-grid-events.tag');
require('./schedule-grid.tag');

riot.mount('schedule-grid', { schedule_api: schedule_api, schedule_filters: schedule_filters });