var dispatcher = require('./feedback-form-dispatcher.js');
var schedule_api     = require('./schedule-api.js');

require('./event-comments.tag');
require('./feedback-form.tag');
require('./feedback-form-comments.tag');
require('./event-action-buttons.tag');
require('./share-buttons.tag');

riot.mount('share-buttons');
riot.mount('event-action-buttons',  { schedule_api: schedule_api })
riot.mount('event-comments');
riot.mount('feedback-form');
riot.mount('feedback-form-comments', { dispatcher: dispatcher });
