//require('./event-comments.tag');
//riot.mount('event-comments');
require('./event-action-buttons.tag');
require('./share-buttons.tag');
var schedule_api     = require('./schedule-api.js');

riot.mount('share-buttons');
riot.mount('event-action-buttons',  { schedule_api: schedule_api })