require('./event-action-buttons.tag');

var schedule_api     = require('./schedule-api.js');

riot.mount('event-action-buttons',  { schedule_api: schedule_api });