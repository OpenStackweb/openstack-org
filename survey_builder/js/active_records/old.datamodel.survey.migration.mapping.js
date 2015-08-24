(function( $ ){

    $.entwine('ss', function ($) {

        $('#Form_ItemEditForm_OriginTable').entwine({
            initialize: function()
            {
                $(this).chosen().change(function (){

                    var table  = $(this).val();
                    var fields = origin_fields[table];

                    var ddl_fields = $('#Form_ItemEditForm_OriginField');

                    $('option', ddl_fields).remove();

                    var option = new Option('-- select an origin field --', '');
                    ddl_fields.append($(option));

                    $.each(fields, function(index, value) {
                        var option = new Option(value, value);
                        ddl_fields.append($(option));
                    });

                    ddl_fields.trigger("liszt:updated");
                });
            },
            onmatch: function ()
            {
                this.initialize();
            }
        });

    });

// End of closure.
}( jQuery));