var dispatcher = require('./survey-report-view-dispatcher.js');
var api        = require('./survey-report-view-api.js');

require('./survey-report-filters.tag');
require('./survey-report-sections.tag');
require('./survey-report-dashboard.tag');

riot.mount('survey-report-filters', { api: api, dispatcher: dispatcher });
riot.mount('survey-report-sections', { api: api, dispatcher: dispatcher  });
riot.mount('survey-report-dashboard', { api: api, dispatcher: dispatcher });

jQuery(document).ready(function($) {
    $('#report-templates').change(function(){
        api.getTemplate($(this).val());
    });

    api.getTemplate($('#report-templates').val());

});
