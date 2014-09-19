jQuery(document).ready(function($){

    var form = $("#distribution_form");

    if(form.length > 0){

        form.marketplace_type_header();

        $("#components_form").components();
        $("#hypervisors_form").hypervisors();
        $("#guest_os_form").guest_os();
        $("#videos-form").videos();
        $("#support-channels-form").support_channels();
        $("#additional-resources-form").additional_resources();

        //if we are editing data, load it ...
        if(typeof(distribution)!=='undefined'){
            //populate form and widgets
            $("#company_id",form).val(distribution.company_id);
            $("#company_id").trigger("chosen:updated");
            $("#name",form).val(distribution.name);
            $("#overview",form).val(distribution.overview);
            $("#call_2_action_uri",form).val(distribution.call_2_action_uri);
            if(distribution.active){
                $('#active',form).prop('checked',true);
            }
            else{
                $('#active',form).prop('checked',false);
            }
            $("#id",form).val(distribution.id);
            //reload widgets
            $("#components_form").components('load',distribution.capabilities);
            $("#hypervisors_form").hypervisors('load',distribution.hypervisors);
            $("#guest_os_form").guest_os('load',distribution.guest_os);
            $("#videos-form").videos('load',distribution.videos);
            $("#support-channels-form").support_channels('load',distribution.regional_support);
            $("#additional-resources-form").additional_resources('load',distribution.additional_resources);
        }

        $('.save-distribution').click(function(event){
            event.preventDefault();
            event.stopPropagation();
            var button =  $(this);
            if(button.prop('disabled')){
                return false;
            }
            var form_validator = form.marketplace_type_header('getFormValidator');
            form_validator.settings.ignore = ".add-comtrol";
            var is_valid = form.valid();
            if(!is_valid) return false;
            form_validator.resetForm();
            var additional_resources = $("#additional-resources-form").additional_resources('serialize');
            var regional_support     = $("#support-channels-form").support_channels('serialize');
            var capabilities         = $("#components_form").components('serialize');
            var guest_os             = $("#guest_os_form").guest_os('serialize');
            var hypervisors          = $("#hypervisors_form").hypervisors('serialize');
            var videos               = $("#videos-form").videos('serialize');

            if(additional_resources !== false &&
                regional_support    !== false &&
                capabilities        !== false &&
                guest_os            !== false &&
                hypervisors         !== false &&
                videos              !== false){

                //create distribution object and POST it
                var distribution = {};
                distribution.id                      = parseInt($("#id",form).val());
                distribution.company_id              = parseInt($("#company_id",form).val());
                distribution.name                    = $("#name",form).val();
                distribution.overview                = $("#overview",form).val();
                distribution.call_2_action_uri       = $("#call_2_action_uri",form).val();
                distribution.active                  = $('#active',form).is(":checked");
                distribution.videos                  = videos;
                distribution.hypervisors             = hypervisors;
                distribution.guest_os                = guest_os;
                distribution.capabilities            = capabilities;
                distribution.regional_support        = regional_support;
                distribution.additional_resources    = additional_resources;

                var type = distribution.id > 0 ?'PUT':'POST';

                $('.save-distribution').prop('disabled',true);
                $.ajax({
                    type: type,
                    url: 'api/v1/marketplace/distributions',
                    data: JSON.stringify(distribution),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function (data,textStatus,jqXHR) {
                        window.location = listing_url;
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        $('.save-distribution').prop('disabled',false);
                        ajaxError(jqXHR, textStatus, errorThrown);
                    }
                });
            }
            return false;
        });
    }
});
