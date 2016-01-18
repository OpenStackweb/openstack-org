(function( $ ){

    $.entwine('ss', function ($) {

        $('#Form_ItemEditForm_OriginSurveyID').entwine({
            initialize: function()
            {
                var ddl_fields = $('#Form_ItemEditForm_OriginFieldID');

                $(this).chosen().change(function (){
                    updateOriginField($(this).val(), ddl_fields);
                });

                updateOriginField($('#Form_ItemEditForm_OriginSurveyID').val(), ddl_fields);
            },
            onmatch: function ()
            {
                this.initialize();
            }
        });

    });

    function updateOriginField(template_id, ddl_fields)
    {
        var questions    = templates[template_id].questions;

        var ddl_fields      = $('#Form_ItemEditForm_OriginFieldID');
        var origin_field_id = ddl_fields.attr('data-value');
        $('option', ddl_fields).remove();

        var option = new Option('-- select an origin field --', '');
        ddl_fields.append($(option));

        $.each(questions, function(index, question) {
            var option = new Option(question.name, question.id);
            ddl_fields.append($(option));
        });
        if(origin_field_id !== null && origin_field_id != '')
        {
            ddl_fields.val(origin_field_id);
            ddl_fields.attr('data-value', '');
        }
        ddl_fields.trigger("liszt:updated");
    }

// End of closure.
}( jQuery));