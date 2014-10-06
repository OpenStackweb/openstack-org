jQuery(document).ready(function($) {
    var form = $("#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters");
    var form_validator = null;

    if(form.length > 0){
        var start_date_id = '#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters_start_date';
        var end_date_id = '#DeploymentSurveyDeploymentsFilters_DeploymentSurveyDeploymentsFilters_end_date';

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
});
