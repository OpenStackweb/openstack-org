require('./schedule-my-schedule.tag');
var schedule_api     = require('./schedule-api.js');

riot.mount('schedule-my-schedule', { schedule_api: schedule_api });