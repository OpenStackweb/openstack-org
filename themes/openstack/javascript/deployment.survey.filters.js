jQuery(document).ready(function($) {
    var form = $("#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters");
    var form_validator = null;

    if(form.length > 0){
        var start_date_id = '#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters_date-from';
        var end_date_id = '#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters_date-to';

        $(start_date_id).datetimepicker({
            format:'Y/m/d H:i',
            onShow:function( ct ){
                this.setOptions({
                    maxDate:$(end_date_id).val()?$(end_date_id).val():false
                })
            },
            timepicker:false
        });
        $(end_date_id).datetimepicker({
            format:'Y/m/d H:i',
            onShow:function( ct ){
                this.setOptions({
                    minDate:$(start_date_id).val()?$(start_date_id).val():false
                })
            },
            timepicker:false
        });
    }

    $('.cvs_download_link').click(function(){
        var filter_box = $(this).siblings('.export_filters');
        $('.export_filters').not(filter_box).slideUp(500).addClass('hidden')

        if (filter_box.hasClass('hidden')) {
            filter_box.slideDown(500).removeClass('hidden');
        } else {
            filter_box.slideUp(500).addClass('hidden');
        }

        return false;
    })
});
