(function($) {
    //hide the importer upload field on load
    $("div.csv-importer").entwine({
        onmatch: function() {
            this.hide();
        }
    });

    $.entwine('ss', function($) {
        $('.ss-gridfield button.toggle-csv-fields.action').entwine({
            //show upload field when button is clicked
            onclick: function(){
                //change entwine scope
                $('div.csv-importer').entwine('.', function($){
                    this.toggle();
                });
            }
        });
    });

    $(".import-upload-csv-field").entwine({
        //when file has uploaded, change url to the field mapper
        onmatch: function() {
            this.on('fileuploaddone', function(e,data){
                e.preventDefault();

                if(data.result[0].hasOwnProperty('error')){
                    $('div.csv-importer-error').html(data.result[0].error);
                    $('div.csv-importer-error').show();
                    return;
                }

                if($('.ss-gridfield .auto-refresh-button button').length > 0)
                    $('.ss-gridfield .auto-refresh-button button').trigger('click');
            });
        }
    });

}(jQuery));