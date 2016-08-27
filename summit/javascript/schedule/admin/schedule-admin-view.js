var api               = require('./schedule-admin-view-api.js');
var dispatcher        = require('./schedule-admin-view-dispatcher.js');
var published_store   = require('./published-events-store.js');
var unpublished_store = require('./unpublished-events-store.js');

require('./../../../css/summit-admin-schedule.scss');
require('./schedule-admin-view-schedule-event.tag');
require('./schedule-admin-view-published-filters.tag');
require('./schedule-admin-view-published.tag');
require('./schedule-admin-view-published-results.tag');
require('./schedule-admin-view-empty-spots.tag');
require('./schedule-admin-view-unpublished-filters.tag');
require('./schedule-admin-view-unpublished-event.tag');
require('./schedule-admin-view-unpublished.tag');
require('./schedule-admin-view-published-bulk-actions.tag');
require('./schedule-admin-view-unpublished-bulk-actions.tag');

riot.mount('schedule-admin-view-published-bulk-actions', { published_store: published_store });
riot.mount('schedule-admin-view-unpublished-bulk-actions', { unpublished_store: unpublished_store } );
riot.mount('schedule-admin-view-published-filters', { dispatcher: dispatcher, api: api });
riot.mount('schedule-admin-view-published', { api: api, dispatcher: dispatcher, published_store: published_store, unpublished_store: unpublished_store });
riot.mount('schedule-admin-view-published-results', { api: api, dispatcher: dispatcher, published_store: published_store });
riot.mount('schedule-admin-view-empty-spots', { api: api, dispatcher: dispatcher, published_store: published_store });
riot.mount('schedule-admin-view-unpublished-filters', { api: api, dispatcher: dispatcher });
riot.mount('schedule-admin-view-unpublished', { api: api , unpublished_store : unpublished_store , dispatcher: dispatcher});