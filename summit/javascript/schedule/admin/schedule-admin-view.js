var api = require('./schedule-admin-view-api.js');

require('./schedule-admin-view-filters.tag');
require('./schedule-admin-view-schedule-event.tag');
require('./schedule-admin-view.tag');

riot.mount('schedule-admin-view-filters', { api: api });
riot.mount('schedule-admin-view', { api: api });