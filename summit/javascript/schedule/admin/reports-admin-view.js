var dispatcher = require('./reports-admin-view-dispatcher.js');

require('./reports-admin-speaker-report.tag');
require('./reports-admin-room-report.tag');
require('./reports-admin-presentation-report.tag');
require('./reports-admin-video-report.tag');
require('./reports-admin-rsvp-report.tag');
require('./reports-admin-track-questions-report.tag');
require('./reports-admin-presentations-company-report.tag');
require('./reports-admin-container.tag');

riot.mount('reports-admin-container',{ dispatcher: dispatcher });