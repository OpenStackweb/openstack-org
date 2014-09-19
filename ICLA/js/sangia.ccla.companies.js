jQuery(document).ready(function($) {

    $('.ccla_checkbox').click(function(event){
        var sign = $(this).is(":checked");

        if(!sign && !confirm("Are you sure...?")){
            event.preventDefault();
            event.stopPropagation();
            return false;
        }

        var company_id = $(this).attr('data-company-id');
        var url        = 'api/v1/ccla/companies/'+company_id+'/sign';
        var verb       = sign?'PUT':'DELETE';

        $.ajax({
            async:true,
            type: verb,
            url: url,
            dataType: "json",
            success: function (data,textStatus,jqXHR) {
                var td = $('#ccla_date_'+company_id);
                if(sign){
                    var date = data.sign_date.date;
                    td.html(date);
                }
                else{
                    td.html('');
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert( "Request failed: " + textStatus );
            }
        });
    });
});