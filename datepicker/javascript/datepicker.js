var DatePickerFieldHandler = function(){
    var $ = jQuery;

    this.initAll = function(){
        var fnc = this.initById;
        $('.DatePickerField').each(function(){
            fnc('#' + $(this).attr('id'));
        });
    }

    this.initById = function(id){
        var date_picker = $(id);
        date_picker.datepicker({
            dateFormat: 'yy-mm-dd',
            onSelect:function(text,inst){
                var dependant = $(this).attr('data-dependant-on');
                if(dependant){
                    var date_dependant = $('#'+dependant);
                    date_dependant.val($(this).val());
                }
            }
        });
    }
}

datePickerFieldHandler = new DatePickerFieldHandler();

//check if prototype wrapper is required.
if(typeof Behaviour == 'object'){
    Behaviour.register({
        '.DatePickerField' : {
            initialise : function(){
                datePickerFieldHandler.initById('#' + this.id);
            }
        }
    });
}

jQuery(document).ready(function(){
    datePickerFieldHandler.initAll();
});