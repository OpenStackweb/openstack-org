(function ($) {

    var methods = {

        init: function(id){

            var ctrl_container = $(this);

            $('.survey-radio-matrix-field-additional-rows-select', ctrl_container).change(function (event){

                var ddl   = $(this);
                var label = $('option:selected', ddl).text();
                var id    = ddl.val();
                if(id === '') return;

                $('option:selected', ddl).remove();
                var html = '<tr><td>'+label+'</td>';
                var cols = eval(ctrl_container.attr('id')+'_columns');

                for(var i=0 ; i < cols.length; i++)
                {
                    var c = cols[i];
                    html += ' <td class="input-cell"><input data-row-id="'+id+'" class="radio_'+id+' radio_opt"  name="'+ctrl_container.attr('id')+'_'+id+'" id="'+id+'_'+c.id+'" type="radio" data-col-id="'+c.id+'"/></td>';
                }
                html +='</tr>';
                $(html).insertBefore($('.tr-add-container', ctrl_container));

                if($('option', ddl).length === 1)
                {
                    $('.tr-add-container', ctrl_container).hide();
                }
            });

            $('.radio_opt', ctrl_container).live('click', function(evt)
            {
                var hidden = $('.ctrl_hidden_value',ctrl_container);
                var state  = '';
                $.each($('.radio_opt:checked', ctrl_container), function(index , radio){
                    var row_id = $(radio).attr('data-row-id');
                    var col_id = $(radio).attr('data-col-id');
                    state += row_id+':'+col_id+',';
                });

                hidden.val(state.substring(0, state.length - 1));
            });

            $('.survey-radui-button-matrix-clear', ctrl_container).click(function(evt){
                $('.radio_opt', ctrl_container).prop('checked', false);
                $(ctrl_container).trigger('table_clear',false);
                var hidden = $('.ctrl_hidden_value',ctrl_container);
                hidden.val('');
                evt.preventDefault();
                return false;
            });
        }

    };

    $.fn.survey_radio_button_matrix_field = function(methodOrOptions) {
        if ( methods[methodOrOptions] ) {
            return methods[ methodOrOptions ].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof methodOrOptions === 'object' || ! methodOrOptions ) {
            // Default to "init"
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  methodOrOptions + ' does not exist on jQuery.survey_radio_button_matrix_field' );
        }
    };
})(jQuery);