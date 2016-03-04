var survey_report_view_api                  = riot.observable();
var api_base_url                            = 'api/v1/survey_report';

survey_report_view_api.TEMPLATE_RETRIEVED   = 'TEMPLATE_RETRIEVED';
survey_report_view_api.REPORT_RETRIEVED     = 'REPORT_RETRIEVED';
survey_report_view_api.CLEAR_REPORT         = 'CLEAR_REPORT';

survey_report_view_api.getTemplate = function (template_id)
{
    $('body').ajax_loader();

    var url = api_base_url+'/report_template/'+template_id;
    $.getJSON(url,{},function(data){
        survey_report_view_api.trigger(survey_report_view_api.TEMPLATE_RETRIEVED, data);
        survey_report_view_api.getReport();
    });
}

survey_report_view_api.getReport = function ()
{
    $('body').ajax_loader();
    var template_id = $('#report-templates').val();
    var section_id  = $('.section.active').data("section-id");
    var filters = [];

    $('.report_filter').each(function(){
        if ($(this).val())
            filters.push({id: $(this).data('qid'), value: $(this).val()})
    });

    if (template_id && section_id) {
        var url = api_base_url+'/report/'+template_id;
        var params = {filters:JSON.stringify(filters),section_id:section_id};
        $.getJSON(url,params,function(data){
            survey_report_view_api.trigger(survey_report_view_api.REPORT_RETRIEVED, data);
        });
    } else {
        survey_report_view_api.trigger(survey_report_view_api.CLEAR_REPORT);
    }

}


module.exports = survey_report_view_api;