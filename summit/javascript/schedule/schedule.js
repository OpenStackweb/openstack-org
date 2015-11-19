require("!style!css!./../../../themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

var schedule_api = require('./schedule-api.js');
require('./schedule-main-filters.tag');
require('./schedule-grid.tag');

riot.mount('schedule-main-filters', { schedule_api: schedule_api });
riot.mount('schedule-grid', { schedule_api: schedule_api });