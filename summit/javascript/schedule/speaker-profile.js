require("./../../../themes/openstack/bower_assets/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css");

var schedule_api     = require('./schedule-api.js');

require('./speaker-profile-events.tag');
require('./schedule-event.tag');

riot.mount('speaker-profile-events',{ schedule_api: schedule_api});