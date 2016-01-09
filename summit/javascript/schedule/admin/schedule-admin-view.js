var api               = require('./schedule-admin-view-api.js');
var dispatcher        = require('./schedule-admin-view-dispatcher.js');
var unpublished_store = require('./unpublished-events-store.js');

require('./../../../css/summit-admin-schedule.scss')
require('./schedule-admin-view-schedule-event.tag');
require('./schedule-admin-view-published-filters.tag');
require('./schedule-admin-view-published.tag');
require('./schedule-admin-view-unpublished-filters.tag');
require('./schedule-admin-view-unpublished-event.tag');
require('./schedule-admin-view-unpublished.tag');

riot.mount('schedule-admin-view-published-filters', { api: api , dispatcher: dispatcher});
riot.mount('schedule-admin-view-published', { api: api, dispatcher: dispatcher, unpublished_store: unpublished_store });
riot.mount('schedule-admin-view-unpublished-filters', { api: api, dispatcher: dispatcher });
riot.mount('schedule-admin-view-unpublished', { api: api , unpublished_store : unpublished_store , dispatcher: dispatcher});