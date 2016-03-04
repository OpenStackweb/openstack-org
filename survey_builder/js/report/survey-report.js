jQuery(document).ready(function($) {
    $('#report-templates').change(function(){
        getSectionsAndFilters(($this).val());
    });
});

getSectionsAndFilters(template_id) {
    $('body').ajax_loader();

    $.getJSON('api/v1/surveys/template/'+template_id,{},function(data){
        riot.update();
        $('body').ajax_loader('stop');
    });
}