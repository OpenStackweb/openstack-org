var schedule_api = require('./schedule-api.js');
require('./schedule-grid.tag');
riot.mount('schedule-grid', { schedule_api: schedule_api });